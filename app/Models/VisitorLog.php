<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model para a tabela `visitor_logs`
 * 
 * Rastreia visitantes não cadastrados para análise de métricas,
 * desempenho e consentimento de cookies. Registros com mais de 90 dias
 * são removidos diariamente pelo Job CleanOldVisitorLogs.
 */
class VisitorLog extends Model
{
    protected $table = 'visitor_logs';

    protected $fillable = [
        'ip_address',
        'user_agent',
        'country',
        'region',
        'city',
        'latitude',
        'longitude',
        'page_url',
        'referrer_url',
        'cookies_consented',
    ];

    protected $casts = [
        'cookies_consented' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    /**
     * Scope para registros com mais de N dias
     */
    public function scopeOlderThanDays($query, int $days)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }
}
