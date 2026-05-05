<?php

namespace App\Services;

use App\Models\User;
use App\Models\RefreshToken;
use App\Enums\UserLevel;
use App\Services\DeviceFingerprintService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Service para Autenticação
 * 
 * Centraliza toda a lógica de autenticação:
 * - Login com OAuth2
 * - Refresh tokens
 * - Validação de escopos
 * - Device fingerprinting
 * - 2FA verification
 */
class AuthService
{
    public function __construct(
        private DeviceFingerprintService $fingerprintService
    ) {}

    /**
     * Realiza login do usuário
     */
    public function login(array $credentials, Request $request): array
    {
        // Converte username para email para compatibilidade com JWT
        $jwtCredentials = [
            'email' => $credentials['username'],
            'password' => $credentials['password'],
        ];

        // Verifica se email foi banido
        if ($this->isEmailBanned($jwtCredentials['email'])) {
            AuditLogService::log('login.failed', null, $request, [
                'email' => $jwtCredentials['email'],
                'reason' => 'banned_email',
            ], 'warning');
            
            throw new \Exception('Conta suspensa.');
        }

        // Autentica
        if (!$token = JWTAuth::attempt($jwtCredentials)) {
            AuditLogService::log('login.failed', null, $request, [
                'email' => $credentials['username'],
                'reason' => 'invalid_credentials',
            ], 'warning');
            
            throw new \Exception('Credenciais inválidas.');
        }

        $user = Auth::user();

        // Verificações
        $this->validateUserForLogin($user);

        // Processa escopos solicitados
        $requestedScopes = $this->parseScopes($request->input('scope'));
        $grantedScopes = $this->validateScopes($requestedScopes, $user);

        // Device Fingerprinting
        $fingerprintResult = $this->fingerprintService->processLogin($user->id, $request);

        // Se 2FA está ativado e é novo dispositivo, requer verificação
        if ($user->two_factor_enabled && $fingerprintResult['is_new_device']) {
            return [
                'requires_2fa' => true,
                'temp_token' => JWTAuth::claims([
                    'temp' => true,
                    'user_id' => $user->id,
                    'fingerprint' => $fingerprintResult['fingerprint'],
                ])->fromUser($user),
            ];
        }

        // Atualiza último login
        $this->updateLastLogin($user, $request);

        // Log de sucesso
        AuditLogService::log('login', $user->id, $request, [
            'fingerprint' => $fingerprintResult['fingerprint'],
            'is_new_device' => $fingerprintResult['is_new_device'],
        ]);

        // Gera tokens
        return $this->generateTokenResponse($user, $grantedScopes, $request, $fingerprintResult);
    }

    /**
     * Realiza refresh token
     */
    public function refreshToken(string $refreshToken, Request $request): array
    {
        // Valida o refresh token
        $tokenData = RefreshToken::validate($refreshToken);

        if (!$tokenData) {
            AuditLogService::log('token.refresh_failed', null, $request, [
                'reason' => 'invalid_refresh_token',
            ], 'warning');
            
            throw new \Exception('Refresh token inválido, expirado ou revogado.');
        }

        $user = $tokenData->user;

        // Verifica se usuário ainda é válido
        if (!$user || !$user->ativo) {
            $tokenData->revokeFamily();
            
            AuditLogService::log('token.refresh_failed', $user?->id, $request, [
                'reason' => 'user_invalid',
            ], 'warning');
            
            throw new \Exception('Usuário inválido ou desativado.');
        }

        // Processa escopos (não pode expandir, apenas reduzir)
        $requestedScopes = $this->parseScopes($request->input('scope'));
        $grantedScopes = $this->limitScopes($requestedScopes, $tokenData->scopes);

        // Rotaciona o refresh token
        $newRefreshToken = $tokenData->rotate($grantedScopes, $request->ip(), $request->userAgent());

        // Log de refresh
        AuditLogService::log('token.refresh', $user->id, $request, [
            'old_token_family' => $tokenData->token_family,
            'new_token_family' => $newRefreshToken['family'],
        ]);

        // Gera novo access token
        return $this->generateTokenResponseFromRefresh($user, $grantedScopes, $newRefreshToken);
    }

    /**
     * Registra novo usuário
     */
    public function register(array $userData): User
    {
        return DB::transaction(function () use ($userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'nivel_acesso' => UserLevel::FREE->value,
                'ativo' => true,
                'uuid' => \Illuminate\Support\Str::ulid(),
            ]);

            AuditLogService::log('user.registered', $user->id, request(), [
                'email' => $userData['email'],
            ]);

            return $user;
        });
    }

    /**
     * Verifica se email foi banido
     */
    private function isEmailBanned(string $email): bool
    {
        $emailNormalizado = strtolower(trim($email));
        $hashEmail = hash('sha256', $emailNormalizado);
        
        return DB::table('emails_bloqueados')
            ->where('hash_email', $hashEmail)
            ->exists();
    }

    /**
     * Valida usuário para login
     */
    private function validateUserForLogin(User $user): void
    {
        if (!$user->hasVerifiedEmail()) {
            throw new \Exception('Email não verificado.');
        }

        if (!$user->ativo) {
            throw new \Exception('Conta desativada.');
        }
    }

    /**
     * Atualiza último login do usuário
     */
    private function updateLastLogin(User $user, Request $request): void
    {
        $user->update([
            'ultimo_ip' => $request->ip(),
            'ultimo_login_em' => now(),
        ]);
    }

    /**
     * Gera resposta de token completa
     */
    private function generateTokenResponse(User $user, array $scopes, Request $request, array $fingerprintResult): array
    {
        // Access Token JWT (short-lived: 15 minutos)
        $accessToken = JWTAuth::claims([
            'scopes' => $scopes,
            'token_type' => 'access_token',
            'fingerprint' => $fingerprintResult['fingerprint'],
        ])->fromUser($user);

        // Refresh Token (long-lived: 30 dias com rotação)
        $refreshTokenData = RefreshToken::generate(
            $user,
            $scopes,
            $request->ip(),
            $request->userAgent()
        );

        return [
            'success' => true,
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_in' => 900, // 15 minutos
            'refresh_token' => $refreshTokenData['refresh_token'],
            'refresh_expires_in' => $refreshTokenData['expires_in'],
            'scope' => implode(' ', $scopes),
            'user' => [
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
                'nivel' => $user->nivel_acesso,
                'level_name' => $user->getLevelAttribute()->getName(),
                'level_description' => $user->getLevelAttribute()->getDescription(),
                'level_color' => $user->getLevelAttribute()->getColor(),
                'two_factor_enabled' => $user->two_factor_enabled,
                // Métodos de verificação para frontend
                'is_admin_level' => $user->isAdminLevel(),
                'can_manage_users' => $user->canManageUsers(),
                'can_view_analytics' => $user->canViewAnalytics(),
                'can_moderate_content' => $user->canModerateContent(),
                'has_plus_access' => $user->hasPlusAccess(),
                'has_premium_access' => $user->hasPremiumAccess(),
            ],
            'device_info' => [
                'is_new_device' => $fingerprintResult['is_new_device'],
                'requires_verification' => $fingerprintResult['requires_verification'],
            ],
        ];
    }

    /**
     * Gera resposta de refresh token
     */
    private function generateTokenResponseFromRefresh(User $user, array $scopes, array $refreshTokenData): array
    {
        $accessToken = JWTAuth::claims([
            'scopes' => $scopes,
            'token_type' => 'access_token',
        ])->fromUser($user);

        return [
            'success' => true,
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_in' => 900, // 15 minutos
            'refresh_token' => $refreshTokenData['refresh_token'],
            'refresh_expires_in' => $refreshTokenData['expires_in'],
            'scope' => implode(' ', $scopes),
        ];
    }

    /**
     * Parse de escopos
     */
    private function parseScopes(?string $scopeString): array
    {
        if (empty($scopeString)) {
            return ['read:profile'];
        }

        return array_filter(explode(' ', $scopeString));
    }

    /**
     * Valida escopos baseado no nível do usuário
     */
    private function validateScopes(array $requestedScopes, User $user): array
    {
        $validScopes = [
            'read:profile',
            'write:profile',
            'read:feed',
            'write:interactions',
            'read:messages',
            'write:messages',
            'read:quem-me-deu-like',
            'write:premium',
            'write:shorts',
            'admin:users',
        ];

        $scopes = array_intersect($requestedScopes, $validScopes);

        // Verifica escopos de admin
        if (in_array('admin:users', $scopes) && !$user->canManageUsers()) {
            $scopes = array_diff($scopes, ['admin:users']);
        }

        // Verifica escopos premium
        if (!$user->hasPremiumAccess()) {
            $scopes = array_diff($scopes, ['write:premium']);
        }

        // Verifica escopos Plus
        if (!$user->hasPlusAccess()) {
            $scopes = array_diff($scopes, ['read:quem-me-deu-like', 'write:shorts']);
        }

        return empty($scopes) ? ['read:profile'] : $scopes;
    }

    /**
     * Limita escopos (refresh token não pode expandir)
     */
    private function limitScopes(array $requested, array $allowed): array
    {
        if (empty($requested)) {
            return $allowed;
        }

        return array_intersect($requested, $allowed);
    }
}
