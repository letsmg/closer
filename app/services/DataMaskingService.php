<?php

namespace App\Services;

use App\Models\User;
use App\Policies\UserPolicy;

/**
 * Service para mascaramento de dados sensíveis.
 * 
 * Garante que usuários OPERATIONAL(11) e SUPPORT(12) não tenham acesso
 * a dados protegidos por privacidade (LGPD/ISO 27001):
 * - Geolocalização exata (coordenadas)
 * - Dados financeiros/pagamentos
 * - Logs de rastreamento em tempo real
 * - Endereços IP completos
 */
class DataMaskingService
{
    /**
     * Máscara dados de localização sensíveis para usuários não-admin
     */
    public static function maskLocationData(?array $locationData, User $viewer): ?array
    {
        if (!$locationData) {
            return null;
        }

        $policy = new UserPolicy();

        // ADMIN pode ver dados completos
        if ($policy->viewSensitiveLocation($viewer)) {
            return $locationData;
        }

        // Para OPERATIONAL e SUPPORT: máscara coordenadas
        return array_merge($locationData, [
            'latitude' => self::maskCoordinate($locationData['latitude'] ?? null),
            'longitude' => self::maskCoordinate($locationData['longitude'] ?? null),
            'exact_location' => false,
            'masked' => true,
        ]);
    }

    /**
     * Máscara dados financeiros - apenas ADMIN vê
     */
    public static function maskFinancialData(?array $financialData, User $viewer): ?array
    {
        if (!$financialData) {
            return null;
        }

        $policy = new UserPolicy();

        if ($policy->viewFinancialData($viewer)) {
            return $financialData;
        }

        // Bloqueia completamente dados financeiros para não-admin
        return null;
    }

    /**
     * Máscara endereço IP - apenas ADMIN vê completo
     */
    public static function maskIpAddress(?string $ip, User $viewer): ?string
    {
        if (!$ip) {
            return null;
        }

        $policy = new UserPolicy();

        if ($policy->viewRawIp($viewer)) {
            return $ip;
        }

        // Mostra apenas os primeiros octetos (ex: 192.168.x.x)
        $parts = explode('.', $ip);
        if (count($parts) === 4) {
            return "{$parts[0]}.{$parts[1]}.x.x";
        }

        return '***.***.***.***';
    }

    /**
     * Máscara dados de tracking log
     */
    public static function maskTrackingLog(array $log, User $viewer): array
    {
        $policy = new UserPolicy();

        if ($policy->viewTrackingLogs($viewer)) {
            return $log;
        }

        // Remove dados sensíveis de tracking
        unset($log['latitude'], $log['longitude'], $log['ip_address']);
        $log['location'] = 'Restricted';
        $log['masked'] = true;

        return $log;
    }

    /**
     * Máscara uma coordenada para nível de cidade (aproximado)
     */
    private static function maskCoordinate(?float $coordinate): ?float
    {
        if ($coordinate === null) {
            return null;
        }

        // Arredonda para ~11km de precisão (0.1 graus)
        // Isso esconde a localização exata mas mantém a região/cidade
        return round($coordinate, 1);
    }
}
