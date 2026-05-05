<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

/**
 * Service para Audit Logging
 * 
 * Responsável por:
 * - Registrar ações sensíveis do sistema
 * - Enviar logs para Elasticsearch (opcional)
 * - Sanitizar dados sensíveis
 * - Consultar logs de auditoria
 */
class AuditLogService
{
    private const SENSITIVE_FIELDS = [
        'password', 'password_confirmation', 'token', 'secret',
        'credit_card', 'ssn', 'cpf', 'cnpj', 'api_key'
    ];

    /**
     * Registra ação de auditoria
     */
    public static function log(string $action, ?int $userId, Request $request, array $metadata = [], string $level = 'info'): void
    {
        $logData = [
            'timestamp' => Carbon::now()->toISOString(),
            'action' => $action,
            'user_id' => $userId,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'metadata' => self::sanitizeMetadata($metadata),
            'level' => $level,
        ];

        // Log local (Laravel)
        self::writeLog($logData);

        // Envia para Elasticsearch (se configurado)
        if (config('services.elasticsearch.enabled', false)) {
            self::sendToElasticsearch($logData);
        }
    }

    /**
     * Registra revogação de token
     */
    public static function logRevocation(?int $userId, string $tokenType, Request $request): void
    {
        self::log('token.revoked', $userId, $request, [
            'token_type' => $tokenType,
        ], 'warning');
    }

    /**
     * Sanitiza metadados removendo informações sensíveis
     */
    private static function sanitizeMetadata(array $metadata): array
    {
        return self::recursiveSanitize($metadata);
    }

    /**
     * Sanitização recursiva de dados
     */
    private static function recursiveSanitize(array $data): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = self::recursiveSanitize($value);
            } elseif (is_string($value)) {
                $sanitized[$key] = self::sanitizeValue($key, $value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Sanitiza valor individual
     */
    private static function sanitizeValue(string $key, string $value): string
    {
        // Verifica se é campo sensível
        foreach (self::SENSITIVE_FIELDS as $field) {
            if (str_contains(strtolower($key), strtolower($field))) {
                return '[REDACTED]';
            }
        }

        // Remove null bytes
        $value = str_replace("\0", '', $value);

        // Limita tamanho para evitar logs gigantes
        if (strlen($value) > 1000) {
            return substr($value, 0, 1000) . '...[TRUNCATED]';
        }

        return $value;
    }

    /**
     * Escreve log local
     */
    private static function writeLog(array $logData): void
    {
        $message = sprintf(
            '[AUDIT] %s - User: %s - Action: %s - IP: %s',
            $logData['timestamp'],
            $logData['user_id'] ?? 'guest',
            $logData['action'],
            $logData['ip_address']
        );

        $context = [
            'audit' => $logData,
        ];

        match ($logData['level']) {
            'emergency', 'alert', 'critical' => Log::emergency($message, $context),
            'error' => Log::error($message, $context),
            'warning' => Log::warning($message, $context),
            'notice' => Log::notice($message, $context),
            'info' => Log::info($message, $context),
            'debug' => Log::debug($message, $context),
            default => Log::info($message, $context),
        };
    }

    /**
     * Envia log para Elasticsearch
     */
    private static function sendToElasticsearch(array $logData): void
    {
        try {
            $index = config('services.elasticsearch.index', 'laravel_audit_logs');
            $url = config('services.elasticsearch.url') . "/{$index}/_doc";

            Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, $logData);
        } catch (\Exception $e) {
            Log::error('Failed to send audit log to Elasticsearch', [
                'error' => $e->getMessage(),
                'log_data' => $logData,
            ]);
        }
    }

    /**
     * Consulta logs de auditoria
     */
    public static function query(array $filters = [], int $limit = 100): array
    {
        // Se Elasticsearch estiver disponível, consulta lá
        if (config('services.elasticsearch.enabled', false)) {
            return self::queryElasticsearch($filters, $limit);
        }

        // Senão, consulta logs locais (limitado)
        return self::queryLocalLogs($filters, $limit);
    }

    /**
     * Consulta no Elasticsearch
     */
    private static function queryElasticsearch(array $filters, int $limit): array
    {
        try {
            $index = config('services.elasticsearch.index', 'laravel_audit_logs');
            $url = config('services.elasticsearch.url') . "/{$index}/_search";

            $query = [
                'size' => $limit,
                'sort' => [
                    ['timestamp' => ['order' => 'desc']],
                ],
            ];

            if (!empty($filters)) {
                $query['query'] = [
                    'bool' => [
                        'must' => self::buildElasticsearchFilters($filters),
                    ],
                ];
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, $query);

            if ($response->successful()) {
                $hits = $response->json('hits.hits', []);
                return array_map(fn($hit) => $hit['_source'], $hits);
            }
        } catch (\Exception $e) {
            Log::error('Failed to query Elasticsearch', [
                'error' => $e->getMessage(),
                'filters' => $filters,
            ]);
        }

        return [];
    }

    /**
     * Consulta logs locais (fallback)
     */
    private static function queryLocalLogs(array $filters, int $limit): array
    {
        // Implementação básica - em produção usar arquivo de logs ou banco
        Log::warning('Querying local logs is limited. Consider configuring Elasticsearch for full audit capabilities.');
        return [];
    }

    /**
     * Constrói filtros para Elasticsearch
     */
    private static function buildElasticsearchFilters(array $filters): array
    {
        $esFilters = [];

        foreach ($filters as $field => $value) {
            if (is_array($value)) {
                $esFilters[] = ['terms' => [$field => $value]];
            } else {
                $esFilters[] = ['term' => [$field => $value]];
            }
        }

        return $esFilters;
    }

    /**
     * Limpa logs antigos (maintenance)
     */
    public static function cleanup(int $daysToKeep = 90): void
    {
        try {
            $index = config('services.elasticsearch.index', 'laravel_audit_logs');
            $url = config('services.elasticsearch.url') . "/{$index}/_delete_by_query";

            $cutoffDate = Carbon::now()->subDays($daysToKeep)->toISOString();

            $query = [
                'query' => [
                    'range' => [
                        'timestamp' => [
                            'lt' => $cutoffDate,
                        ],
                    ],
                ],
            ];

            Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, $query);

            Log::info("Audit logs cleanup completed", [
                'cutoff_date' => $cutoffDate,
                'days_to_keep' => $daysToKeep,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to cleanup audit logs', [
                'error' => $e->getMessage(),
                'days_to_keep' => $daysToKeep,
            ]);
        }
    }
}
