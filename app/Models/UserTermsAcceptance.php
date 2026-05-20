<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model para a tabela `user_terms_acceptances`
 * 
 * Persiste o histórico de aceite dos Termos de Uso e Política de Privacidade.
 * Cada registro representa um aceite explícito do usuário a uma versão específica
 * dos documentos legais. O TermsAcceptanceMiddleware consulta esta tabela para
 * validar se o usuário pode acessar funcionalidades do sistema.
 */
class UserTermsAcceptance extends Model
{
    protected $table = 'user_terms_acceptances';

    protected $fillable = [
        'user_id',
        'terms_version',
        'privacy_version',
        'accepted_at',
        'ip_address',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    /**
     * Relacionamento: o aceite pertence a um usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verifica se o usuário tem um aceite válido para as versões atuais dos termos
     */
    public static function hasValidAcceptance(int $userId, string $termsVersion, string $privacyVersion): bool
    {
        return self::where('user_id', $userId)
            ->where('terms_version', $termsVersion)
            ->where('privacy_version', $privacyVersion)
            ->exists();
    }

    /**
     * Registra um novo aceite para o usuário
     */
    public static function recordAcceptance(int $userId, string $termsVersion, string $privacyVersion, ?string $ipAddress = null): self
    {
        return self::create([
            'user_id' => $userId,
            'terms_version' => $termsVersion,
            'privacy_version' => $privacyVersion,
            'accepted_at' => now(),
            'ip_address' => $ipAddress,
        ]);
    }
}
