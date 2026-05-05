<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RefreshToken;
use App\Services\DeviceFingerprintService;
use App\Services\AuditLogService;
use App\Services\AuthService;
use App\Http\Requests\Auth\LoginRequest;
use App\Traits\SanitizesOutput;
use App\Enums\UserLevel;
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
    
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
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
    protected function handlePasswordGrant(Request $request)
    {
        // Validação manual
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'username' => 'required|string|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};:"\\|,.<>\/?]).+$/',
            ],
        ], [
            'username.required' => 'O email é obrigatório.',
            'username.email' => 'O email deve ser válido.',
            'password.required' => 'A senha é obrigatória.',
            'password.string' => 'A senha deve ser um texto.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.regex' => 'A senha deve conter pelo menos 1 letra maiúscula e 1 caractere especial (!@#$%^&*()).',
        ]);

        if ($validator->fails()) {
            return $this->safeJsonResponse([
                'error' => 'invalid_request',
                'error_description' => 'Dados inválidos.',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            $result = $this->authService->login([
                'username' => $request->input('username'),
                'password' => $request->input('password'),
            ], $request);

            if (isset($result['requires_2fa'])) {
                return $this->safeJsonResponse([
                    'error' => 'requires_2fa',
                    'error_description' => 'Autenticação de dois fatores necessária.',
                    'requires_2fa' => true,
                    'temp_token' => $result['temp_token'],
                ], 403);
            }

            return $this->safeJsonResponse($result);

        } catch (\Exception $e) {
            \Log::error('Erro no login: ' . $e->getMessage());
            
            return $this->safeJsonResponse([
                'error' => 'invalid_grant',
                'error_description' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * --------------------------------------------------------------------------
     * REFRESH TOKEN GRANT
     * --------------------------------------------------------------------------
     */
    protected function handleRefreshTokenGrant(Request $request)
    {
        try {
            $result = $this->authService->refreshToken(
                $request->input('refresh_token'),
                $request
            );

            return $this->safeJsonResponse($result);

        } catch (\Exception $e) {
            \Log::error('Erro no refresh token: ' . $e->getMessage());
            
            return $this->safeJsonResponse([
                'error' => 'invalid_grant',
                'error_description' => $e->getMessage(),
            ], 401);
        }
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
                'nivel' => $user->nivel_acesso,
                'level_name' => $user->getLevelAttribute()->getName(),
                'level_description' => $user->getLevelAttribute()->getDescription(),
                'level_color' => $user->getLevelAttribute()->getColor(),
                'two_factor_enabled' => $user->two_factor_enabled,
            ],
            'device_info' => [
                'is_new_device' => $fingerprintResult['is_new_device'],
                'requires_verification' => $fingerprintResult['requires_verification'],
            ],
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
        if (in_array('admin:users', $scopes) && !$user->canManageUsers()) {
            $scopes = array_diff($scopes, ['admin:users']);
        }

        // Verifica escopos premium
        $premiumScopes = ['write:premium', 'read:quem-me-deu-like'];
        
        if (!$user->hasPremiumAccess()) {
            $scopes = array_diff($scopes, $premiumScopes);
        }

        // Verifica escopos Plus
        $plusScopes = ['read:quem-me-deu-like', 'write:shorts'];
        
        if (!$user->hasPlusAccess()) {
            $scopes = array_diff($scopes, $plusScopes);
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
