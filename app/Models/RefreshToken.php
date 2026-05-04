<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Model de Refresh Token
 * 
 * Implementa Short-lived Access Tokens + Long-lived Refresh Tokens
 * Segurança: Refresh tokens são rotacionados (troca por novo ao usar)
 * Família de tokens: Detecta roubo de refresh token
 */
class RefreshToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token_hash',           // Hash do token (nunca armazene o token plano!)
        'token_family',         // Família para detectar roubo
        'scopes',               // Escopos OAuth2
        'expires_at',
        'revoked_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
        'scopes' => 'array',
    ];

    protected $hidden = [
        'token_hash',
    ];

    /**
     * Gera um novo refresh token seguro
     */
    public static function generate(User $user, array $scopes = [], ?string $ip = null, ?string $userAgent = null): array
    {
        $plainToken = Str::random(64); // Token opaco (não JWT)
        $family = Str::random(16);     // Identificador da família

        $refreshToken = self::create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $plainToken),
            'token_family' => $family,
            'scopes' => $scopes,
            'expires_at' => now()->addDays(30), // Refresh token dura 30 dias
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);

        return [
            'refresh_token' => $plainToken,
            'family' => $family,
            'expires_in' => 30 * 24 * 60 * 60, // 30 dias em segundos
        ];
    }

    /**
     * Valida um refresh token
     */
    public static function validate(string $plainToken): ?self
    {
        $hash = hash('sha256', $plainToken);
        
        $token = self::where('token_hash', $hash)
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$token) {
            return null;
        }

        // Verifica se o token foi potencialmente roubado
        // (se houve uso de token revogado da mesma família)
        $compromised = self::where('token_family', $token->token_family)
            ->where('revoked_at', '!=', null)
            ->where('revoked_at', '>', $token->created_at)
            ->exists();

        if ($compromised) {
            // Possível roubo! Revoga toda a família
            self::where('token_family', $token->token_family)
                ->update(['revoked_at' => now()]);
            
            // Notifica usuário (implementar)
            // event(new PotentialTokenTheft($token->user));
            
            return null;
        }

        return $token;
    }

    /**
     * Rotaciona o refresh token (troca por novo)
     * Segurança: Impede reuse de refresh tokens
     */
    public function rotate(array $scopes = [], ?string $ip = null, ?string $userAgent = null): array
    {
        // Revoga o token atual
        $this->update(['revoked_at' => now()]);

        // Gera novo token da mesma família
        return self::generate(
            $this->user, 
            $scopes ?: $this->scopes, 
            $ip, 
            $userAgent
        );
    }

    /**
     * Verifica se tem escopo específico
     */
    public function hasScope(string $scope): bool
    {
        return in_array($scope, $this->scopes ?? [], true);
    }

    /**
     * Revoga todos os tokens de um usuário
     */
    public static function revokeAllForUser(int $userId): void
    {
        self::where('user_id', $userId)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);
    }

    /**
     * Revoga toda a família de tokens
     */
    public function revokeFamily(): void
    {
        self::where('token_family', $this->token_family)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);
    }

    /**
     * Relacionamento com usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verifica se está expirado
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Verifica se está revogado
     */
    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }
}
