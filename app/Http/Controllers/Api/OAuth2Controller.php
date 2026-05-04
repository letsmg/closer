<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RefreshToken;
use App\Services\DeviceFingerprintService;
use App\Services\AuditLogService;
use App\Http\Requests\Auth\LoginRequest;
use App\Traits\SanitizesOutput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * OAuth2 Controller com JWT + Refresh Tokens + Escopos
 * 
 * Segurança implementada:
 * - Short-lived Access Tokens (15 minutos)
 * - Long-lived Refresh Tokens (30 dias com rotação)
 * - Escopos OAuth2 granulares
 * - Detecção de roubo de tokens (token families)
 */
class OAuth2Controller extends Controller
{
    use SanitizesOutput;
    
    protected DeviceFingerprintService $fingerprintService;

    public function __construct(DeviceFingerprintService $fingerprintService)
    {
        $this->fingerprintService = $fingerprintService;
    }
    /**
     * Escopos padrão disponíveis no sistema
     */
    const SCOPES = [
        'read:profile' => 'Ler dados do perfil do usuário',
        'write:profile' => 'Modificar dados do perfil',
        'read:feed' => 'Acessar feed de perfis',
        'write:interactions' => 'Dar like, dislike, match',
        'read:messages' => 'Ler mensagens de chat',
        'write:messages' => 'Enviar mensagens',
        'read:matches' => 'Ver matches',
        'write:photos' => 'Upload e gerenciamento de fotos',
        'read:shorts' => 'Ver e criar shorts',
        'write:premium' => 'Ações exclusivas de usuários premium',
        'admin:users' => 'Administrar usuários (apenas admins)',
    ];

    /**
     * --------------------------------------------------------------------------
     * TOKEN ENDPOINT (OAuth2 Standard)
     * --------------------------------------------------------------------------
     * 
     * POST /oauth/token
     * grant_type: password | refresh_token
     */
    public function token(Request $request)
    {
        $grantType = $request->input('grant_type');

        return match ($grantType) {
            'password' => $this->handlePasswordGrant($request),
            'refresh_token' => $this->handleRefreshTokenGrant($request),
            default => response()->json([
                'error' => 'unsupported_grant_type',
                'error_description' => 'Tipo de grant não suportado. Use password ou refresh_token.'
            ], 400),
        };
    }

    /**
     * --------------------------------------------------------------------------
     * PASSWORD GRANT (Login com escopos)
     * --------------------------------------------------------------------------
     */
    protected function handlePasswordGrant(LoginRequest $request)
    {
        // Validação e sanitização já feitas no LoginRequest

        // Verifica se email foi banido (já sanitizado no request)
        $emailNormalizado = strtolower(trim($request->input('username')));
        $hashEmail = $this->gerarHashEmail($emailNormalizado);
        
        $emailBanido = DB::table('emails_bloqueados')
            ->where('hash_email', $hashEmail)
            ->exists();

        if ($emailBanido) {
            return $this->safeJsonResponse([
                'error' => 'access_denied',
                'error_description' => 'Conta suspensa.'
            ], 403);
        }

        // Autentica com dados sanitizados
        $credentials = $request->getCredentials();

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'error' => 'invalid_grant',
                    'error_description' => 'Credenciais inválidas.'
                ], 401);
            }

            $user = Auth::user();

            // Verificações
            if (!$user->hasVerifiedEmail()) {
                return response()->json([
                    'error' => 'access_denied',
                    'error_description' => 'Email não verificado.'
                ], 403);
            }

            if (!$user->ativo) {
                return response()->json([
                    'error' => 'access_denied',
                    'error_description' => 'Conta desativada.'
                ], 403);
            }

            // Processa escopos solicitados
            $requestedScopes = $this->parseScopes($request->input('scope'));
            $grantedScopes = $this->validateScopes($requestedScopes, $user);

            // Atualiza último login
            $user->update([
                'ultimo_ip' => $request->ip(),
                'ultimo_login_em' => now(),
            ]);

            // Gera tokens
            return $this->generateTokenResponse($user, $grantedScopes, $request);

        } catch (JWTException $e) {
            \Log::error('Erro JWT no login: ' . $e->getMessage());
            return response()->json([
                'error' => 'server_error',
                'error_description' => 'Erro interno ao gerar token.'
            ], 500);
        }
    }

    /**
     * --------------------------------------------------------------------------
     * REFRESH TOKEN GRANT
     * --------------------------------------------------------------------------
     */
    protected function handleRefreshTokenGrant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required|string',
            'scope' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'invalid_request',
                'error_description' => 'Refresh token obrigatório.'
            ], 400);
        }

        // Valida o refresh token
        $refreshToken = RefreshToken::validate($request->refresh_token);

        if (!$refreshToken) {
            return response()->json([
                'error' => 'invalid_grant',
                'error_description' => 'Refresh token inválido, expirado ou revogado.'
            ], 401);
        }

        $user = $refreshToken->user;

        // Verifica se usuário ainda é válido
        if (!$user || !$user->ativo) {
            $refreshToken->revokeFamily();
            return response()->json([
                'error' => 'invalid_grant',
                'error_description' => 'Usuário inválido ou desativado.'
            ], 401);
        }

        // Processa escopos (não pode expandir, apenas reduzir)
        $requestedScopes = $this->parseScopes($request->input('scope'));
        $grantedScopes = $this->limitScopes($requestedScopes, $refreshToken->scopes);

        // Rotaciona o refresh token (segurança: impede reuse)
        $newRefreshToken = $refreshToken->rotate($grantedScopes, $request->ip(), $request->userAgent());

        // Gera novo access token
        return $this->generateTokenResponseFromRefresh($user, $grantedScopes, $newRefreshToken);
    }

    /**
     * --------------------------------------------------------------------------
     * REVOKE TOKEN
     * --------------------------------------------------------------------------
     */
    public function revoke(Request $request)
    {
        $token = $request->input('token');
        $tokenTypeHint = $request->input('token_type_hint', 'access_token');

        if ($tokenTypeHint === 'refresh_token') {
            $refreshToken = RefreshToken::validate($token);
            if ($refreshToken) {
                $refreshToken->update(['revoked_at' => now()]);
            }
        }

        // Sempre tenta revogar no JWT também
        try {
            JWTAuth::setToken($token)->invalidate();
        } catch (\Exception $e) {
            // Ignora erros
        }

        return response()->json([], 200);
    }

    /**
     * --------------------------------------------------------------------------
     * INTROSPECT TOKEN (Verificar validade e escopos)
     * --------------------------------------------------------------------------
     */
    public function introspect(Request $request)
    {
        $token = $request->input('token');
        
        try {
            $payload = JWTAuth::setToken($token)->getPayload();
            
            return response()->json([
                'active' => true,
                'sub' => $payload->get('sub'),
                'exp' => $payload->get('exp'),
                'iat' => $payload->get('iat'),
                'scope' => $payload->get('scopes', []),
                'client_id' => $payload->get('iss'),
            ]);

        } catch (\Exception $e) {
            return response()->json(['active' => false], 200);
        }
    }

    /**
     * --------------------------------------------------------------------------
     * GERAÇÃO DE TOKENS
     * --------------------------------------------------------------------------
     */
    protected function generateTokenResponse(User $user, array $scopes, Request $request): array
    {
        // Access Token JWT (short-lived: 15 minutos)
        $accessToken = JWTAuth::claims([
            'scopes' => $scopes,
            'token_type' => 'access_token',
        ])->fromUser($user);

        // Refresh Token (long-lived: 30 dias com rotação)
        $refreshTokenData = RefreshToken::generate(
            $user,
            $scopes,
            $request->ip(),
            $request->userAgent()
        );

        return [
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_in' => 900, // 15 minutos
            'refresh_token' => $refreshTokenData['refresh_token'],
            'refresh_expires_in' => $refreshTokenData['expires_in'],
            'scope' => implode(' ', $scopes),
            'user' => [
                'uuid' => $user->uuid, // ULID público
                'name' => $user->name,
                'email' => $user->email,
                'nivel' => $user->nivel ?? 0,
            ],
        ];
    }

    protected function generateTokenResponseFromRefresh(User $user, array $scopes, array $refreshTokenData): array
    {
        $accessToken = JWTAuth::claims([
            'scopes' => $scopes,
            'token_type' => 'access_token',
        ])->fromUser($user);

        return [
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_in' => 900, // 15 minutos
            'refresh_token' => $refreshTokenData['refresh_token'],
            'refresh_expires_in' => $refreshTokenData['expires_in'],
            'scope' => implode(' ', $scopes),
        ];
    }

    /**
     * --------------------------------------------------------------------------
     * UTILITÁRIOS DE ESCOPOS
     * --------------------------------------------------------------------------
     */
    protected function parseScopes(?string $scopeString): array
    {
        if (empty($scopeString)) {
            return ['read:profile']; // Escopo padrão mínimo
        }

        return array_filter(explode(' ', $scopeString));
    }

    protected function validateScopes(array $requestedScopes, User $user): array
    {
        // Verifica escopos válidos
        $validScopes = array_keys(self::SCOPES);
        $scopes = array_intersect($requestedScopes, $validScopes);

        // Verifica escopos de admin
        if (in_array('admin:users', $scopes) && $user->nivel !== 3) {
            $scopes = array_diff($scopes, ['admin:users']);
        }

        // Verifica escopos premium
        $premiumScopes = ['write:premium', 'read:quem-me-deu-like'];
        $userLevel = $user->nivel ?? 0;
        
        if ($userLevel < 1) { // Não é premium
            $scopes = array_diff($scopes, $premiumScopes);
        }

        return empty($scopes) ? ['read:profile'] : $scopes;
    }

    protected function limitScopes(array $requested, array $allowed): array
    {
        // Ao usar refresh token, não pode expandir escopos
        if (empty($requested)) {
            return $allowed;
        }

        return array_intersect($requested, $allowed);
    }

    /**
     * --------------------------------------------------------------------------
     * FUNÇÕES PRIVADAS
     * --------------------------------------------------------------------------
     */
    private function gerarHashEmail(string $email): string
    {
        return hash('sha256', strtolower(trim($email)));
    }

    /**
     * Lista escopos disponíveis (documentação)
     */
    public function scopes(): array
    {
        return self::SCOPES;
    }
}
