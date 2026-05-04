<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * Audit Logging Service
 * 
 * Registra todas as ações sensíveis para auditoria de segurança.
 * Pode enviar para Elasticsearch, Splunk, ou arquivo de log.
 */
class AuditLogService
{
    /**
     * Ações sensíveis que devem ser logadas
     */
    const SENSITIVE_ACTIONS = [
        'login',
        'login.failed',
        'logout',
        'password.change',
        'password.reset',
        '2fa.enable',
        '2fa.disable',
        'email.verify',
        'profile.update',
        'photo.upload',
        'photo.delete',
        'like',
        'match',
        'block',
        'report',
        'payment',
        'subscription.cancel',
        'device.new',
        'device.revoke',
        'token.revoke',
    ];

    /**
     * Loga uma ação auditável
     */
    public static function log(
        string $action,
        ?int $userId = null,
        ?Request $request = null,
        array $metadata = [],
        string $severity = 'info'
    ): void {
        if (!in_array($action, self::SENSITIVE_ACTIONS, true)) {
            return; // Só loga ações sensíveis
        }

        $logEntry = [
            '@timestamp' => now()->toIso8601String(),
            'service' => config('app.name'),
            'environment' => config('app.env'),
            'action' => $action,
            'user_id' => $userId,
            'severity' => $severity,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'request_id' => $request?->header('X-Request-ID') ?? uniqid(),
            'url' => $request?->fullUrl(),
            'method' => $request?->method(),
            'metadata' => self::sanitizeMetadata($metadata),
        ];

        // Envia para Elasticsearch se configurado
        if (config('services.elasticsearch.enabled')) {
            self::sendToElasticsearch($logEntry);
        }

        // Log local estruturado
        Log::channel('audit')->{$severity}(json_encode($logEntry));
    }

    /**
     * Loga tentativa de login
     */
    public static function logLogin(?int $userId, Request $request, bool $success): void
    {
        $action = $success ? 'login' : 'login.failed';
        $severity = $success ? 'info' : 'warning';

        self::log($action, $userId, $request, [
            'success' => $success,
            'email' => $request->input('email'),
        ], $severity);
    }

    /**
     * Loga atividade de 2FA
     */
    public static function log2FA(int $userId, string $action, Request $request): void
    {
        $allowedActions = ['enable', 'disable', 'verify', 'backup_used'];
        
        if (!in_array($action, $allowedActions, true)) {
            return;
        }

        self::log("2fa.{$action}", $userId, $request, [], 'info');
    }

    /**
     * Loga novo dispositivo
     */
    public static function logNewDevice(int $userId, array $deviceInfo, Request $request): void
    {
        self::log('device.new', $userId, $request, [
            'fingerprint' => $deviceInfo['fingerprint'] ?? null,
            'platform' => $deviceInfo['platform'] ?? null,
            'location' => $deviceInfo['location'] ?? null,
        ], 'warning');
    }

    /**
     * Loga revogação de tokens/sessões
     */
    public static function logRevocation(int $userId, string $type, Request $request): void
    {
        self::log("{$type}.revoke", $userId, $request, [
            'revocation_type' => $type,
        ], 'info');
    }

    /**
     * Loga alteração de dados sensíveis
     */
    public static function logDataChange(
        int $userId,
        string $dataType,
        array $changes,
        Request $request
    ): void {
        self::log('data.change', $userId, $request, [
            'data_type' => $dataType,
            'fields_changed' => array_keys($changes),
            // Não loga valores reais de dados sensíveis!
        ], 'info');
    }

    /**
     * Envia logs para Elasticsearch
     */
    protected static function sendToElasticsearch(array $logEntry): void
    {
        try {
            $esHost = config('services.elasticsearch.host');
            $esIndex = config('services.elasticsearch.index', 'closer-audit');
            
            Http::timeout(5)->post("{$esHost}/{$esIndex}/_doc", $logEntry);
        } catch (\Exception $e) {
            // Falha silenciosa - não quebra a aplicação
            Log::error('Failed to send audit log to Elasticsearch', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove dados sensíveis dos metadados
     */
    protected static function sanitizeMetadata(array $metadata): array
    {
        $sensitiveKeys = ['password', 'token', 'secret', 'credit_card', 'ssn', 'cpf'];
        
        foreach ($metadata as $key => $value) {
            if (in_array(strtolower($key), $sensitiveKeys, true)) {
                $metadata[$key] = '[REDACTED]';
            }
            
            // Recursivamente sanitiza arrays
            if (is_array($value)) {
                $metadata[$key] = self::sanitizeMetadata($value);
            }
        }
        
        return $metadata;
    }

    /**
     * Consulta logs de auditoria (para admins)
     */
    public static function query(array $filters = [], int $limit = 100): array
    {
        // Implementação depende do storage (Elasticsearch, DB, etc.)
        // Exemplo simplificado:
        return [];
    }
}
