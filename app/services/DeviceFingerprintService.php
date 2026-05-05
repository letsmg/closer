<?php

namespace App\Services;

use App\Models\User;
use App\Events\NewDeviceLogin;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

/**
 * Service para Device Fingerprinting
 * 
 * Responsável por:
 * - Gerar fingerprints de dispositivos
 * - Detectar novos dispositivos
 * - Registrar dispositivos conhecidos
 * - Enviar notificações de novos dispositivos
 */
class DeviceFingerprintService
{
    private const CACHE_TTL = 90 * 24 * 60 * 60; // 90 dias

    /**
     * Processa login e verifica dispositivo
     */
    public function processLogin(int $userId, Request $request): array
    {
        $fingerprint = $this->generateFingerprint($request);
        $deviceInfo = $this->getDeviceInfo($request);
        
        // Verifica se é um novo dispositivo
        $isNewDevice = $this->isNewDevice($userId, $fingerprint);
        
        if ($isNewDevice) {
            $this->registerDevice($userId, $fingerprint, $deviceInfo);
            
            // Dispara evento de novo dispositivo
            event(new NewDeviceLogin($userId, $deviceInfo));
        }
        
        return [
            'fingerprint' => $fingerprint,
            'is_new_device' => $isNewDevice,
            'requires_verification' => $isNewDevice,
            'device_info' => $deviceInfo,
        ];
    }

    /**
     * Gera fingerprint único do dispositivo
     */
    private function generateFingerprint(Request $request): string
    {
        $data = [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'accept_language' => $request->header('Accept-Language'),
            'accept_encoding' => $request->header('Accept-Encoding'),
        ];
        
        return hash('sha256', json_encode($data));
    }

    /**
     * Extrai informações do dispositivo
     */
    private function getDeviceInfo(Request $request): array
    {
        $userAgent = $request->userAgent();
        
        return [
            'ip' => $request->ip(),
            'user_agent' => $userAgent,
            'platform' => $this->detectPlatform($userAgent),
            'browser' => $this->detectBrowser($userAgent),
            'location' => $this->getLocationByIp($request->ip()),
        ];
    }

    /**
     * Verifica se é um novo dispositivo
     */
    private function isNewDevice(int $userId, string $fingerprint): bool
    {
        $cacheKey = "user_devices:{$userId}";
        $knownDevices = Cache::get($cacheKey, []);
        
        return !in_array($fingerprint, $knownDevices);
    }

    /**
     * Registra novo dispositivo
     */
    private function registerDevice(int $userId, string $fingerprint, array $deviceInfo): void
    {
        $cacheKey = "user_devices:{$userId}";
        $knownDevices = Cache::get($cacheKey, []);
        
        // Adiciona novo fingerprint
        $knownDevices[] = $fingerprint;
        
        // Limita a 50 dispositivos por usuário
        if (count($knownDevices) > 50) {
            $knownDevices = array_slice($knownDevices, -50);
        }
        
        Cache::put($cacheKey, $knownDevices, self::CACHE_TTL);
    }

    /**
     * Lista dispositivos conhecidos
     */
    public function getKnownDevices(int $userId): array
    {
        $cacheKey = "user_devices:{$userId}";
        return Cache::get($cacheKey, []);
    }

    /**
     * Remove dispositivo específico
     */
    public function removeDevice(int $userId, string $fingerprint): bool
    {
        $cacheKey = "user_devices:{$userId}";
        $knownDevices = Cache::get($cacheKey, []);
        
        $key = array_search($fingerprint, $knownDevices);
        if ($key !== false) {
            unset($knownDevices[$key]);
            Cache::put($cacheKey, array_values($knownDevices), self::CACHE_TTL);
            return true;
        }
        
        return false;
    }

    /**
     * Limpa todos os dispositivos do usuário
     */
    public function clearDevices(int $userId): void
    {
        $cacheKey = "user_devices:{$userId}";
        Cache::forget($cacheKey);
    }

    /**
     * Detecta plataforma (desktop/mobile/tablet)
     */
    private function detectPlatform(string $userAgent): string
    {
        if (preg_match('/Mobile|Android|iPhone|iPad/i', $userAgent)) {
            if (preg_match('/iPad/i', $userAgent)) {
                return 'tablet';
            }
            return 'mobile';
        }
        
        return 'desktop';
    }

    /**
     * Detecta navegador
     */
    private function detectBrowser(string $userAgent): string
    {
        if (preg_match('/Chrome/i', $userAgent)) {
            return 'Chrome';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            return 'Firefox';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            return 'Safari';
        } elseif (preg_match('/Edge/i', $userAgent)) {
            return 'Edge';
        }
        
        return 'Unknown';
    }

    /**
     * Obtém localização por IP (simplificado)
     */
    private function getLocationByIp(string $ip): string
    {
        // Em produção, usar serviço como GeoIP2 ou ipinfo.io
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return 'Localhost';
        }
        
        // Simulação - em produção usar API real
        return 'Unknown Location';
    }
}
