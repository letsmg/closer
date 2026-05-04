<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Device Fingerprinting Service
 * 
 * Detecta logins de novos dispositivos e notifica o usuário.
 * Usa múltiplos fatores para identificar dispositivos únicos.
 */
class DeviceFingerprintService
{
    /**
     * Gera fingerprint único do dispositivo
     */
    public function generateFingerprint(Request $request): string
    {
        $factors = [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'accept_language' => $request->header('Accept-Language'),
            'accept_encoding' => $request->header('Accept-Encoding'),
            'platform' => $this->detectPlatform($request->userAgent()),
            'screen_info' => $request->header('X-Screen-Info'), // Enviado pelo frontend
        ];

        // Hash consistente baseado nos fatores
        return hash('sha256', json_encode($factors));
    }

    /**
     * Verifica se é um novo dispositivo para o usuário
     */
    public function isNewDevice(int $userId, string $fingerprint): bool
    {
        $knownDevices = Cache::get("user:{$userId}:devices", []);
        
        return !in_array($fingerprint, $knownDevices, true);
    }

    /**
     * Registra novo dispositivo para o usuário
     */
    public function registerDevice(int $userId, string $fingerprint, array $metadata = []): void
    {
        $key = "user:{$userId}:devices";
        $devices = Cache::get($key, []);
        
        // Limita a 10 dispositivos por usuário (LRU)
        if (count($devices) >= 10) {
            array_shift($devices);
        }
        
        $devices[] = $fingerprint;
        
        // Armazena por 90 dias
        Cache::put($key, $devices, now()->addDays(90));
        
        // Salva metadados do dispositivo
        Cache::put(
            "device:{$fingerprint}:metadata",
            array_merge($metadata, [
                'first_seen' => now()->toIso8601String(),
                'user_id' => $userId,
            ]),
            now()->addDays(90)
        );
    }

    /**
     * Processa login e detecta novos dispositivos
     */
    public function processLogin(int $userId, Request $request): array
    {
        $fingerprint = $this->generateFingerprint($request);
        $isNew = $this->isNewDevice($userId, $fingerprint);
        
        $deviceInfo = [
            'fingerprint' => $fingerprint,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'platform' => $this->detectPlatform($request->userAgent()),
            'location' => $this->getLocationFromIp($request->ip()),
            'timestamp' => now()->toIso8601String(),
        ];

        if ($isNew) {
            // Registra novo dispositivo
            $this->registerDevice($userId, $fingerprint, $deviceInfo);
            
            // Dispara evento de novo dispositivo
            event(new \App\Events\NewDeviceLogin($userId, $deviceInfo));
            
            // Log de segurança
            Log::warning('Novo dispositivo detectado', [
                'user_id' => $userId,
                'fingerprint' => $fingerprint,
                'ip' => $request->ip(),
            ]);
        }

        return [
            'fingerprint' => $fingerprint,
            'is_new_device' => $isNew,
            'device_info' => $deviceInfo,
            'requires_verification' => $isNew, // Novo dispositivo requer verificação
        ];
    }

    /**
     * Revoga todos os dispositivos de um usuário
     */
    public function revokeAllDevices(int $userId): void
    {
        Cache::forget("user:{$userId}:devices");
        
        Log::info('Todos os dispositivos revogados', ['user_id' => $userId]);
    }

    /**
     * Lista dispositivos conhecidos
     */
    public function listDevices(int $userId): array
    {
        $fingerprints = Cache::get("user:{$userId}:devices", []);
        $devices = [];
        
        foreach ($fingerprints as $fp) {
            $metadata = Cache::get("device:{$fp}:metadata", []);
            if ($metadata) {
                $devices[] = [
                    'fingerprint_short' => substr($fp, 0, 8) . '...',
                    'platform' => $metadata['platform'] ?? 'Unknown',
                    'location' => $metadata['location'] ?? 'Unknown',
                    'ip' => $metadata['ip'] ?? 'Unknown',
                    'first_seen' => $metadata['first_seen'] ?? null,
                ];
            }
        }
        
        return $devices;
    }

    /**
     * Detecta plataforma do User-Agent
     */
    protected function detectPlatform(?string $userAgent): string
    {
        if (!$userAgent) return 'Unknown';
        
        $platforms = [
            'iPhone' => 'iOS',
            'iPad' => 'iOS',
            'Android' => 'Android',
            'Windows' => 'Windows',
            'Mac OS X' => 'macOS',
            'Linux' => 'Linux',
        ];
        
        foreach ($platforms as $pattern => $name) {
            if (str_contains($userAgent, $pattern)) {
                return $name;
            }
        }
        
        return 'Unknown';
    }

    /**
     * Obtém localização aproximada do IP
     */
    protected function getLocationFromIp(string $ip): ?string
    {
        // Usar serviço de geolocalização (MaxMind ou similar)
        // Ou cache de lookups anteriores
        $cacheKey = "geo:{$ip}";
        
        return Cache::remember($cacheKey, now()->addDay(), function () use ($ip) {
            try {
                // Implementação opcional com MaxMind GeoIP2
                // ou API gratuita como ip-api.com (com rate limiting)
                return null;
            } catch (\Exception $e) {
                return null;
            }
        });
    }

    /**
     * Verifica se dispositivo está na lista de bloqueados
     */
    public function isDeviceBlocked(int $userId, string $fingerprint): bool
    {
        $blocked = Cache::get("user:{$userId}:blocked_devices", []);
        return in_array($fingerprint, $blocked, true);
    }

    /**
     * Bloqueia um dispositivo
     */
    public function blockDevice(int $userId, string $fingerprint): void
    {
        $key = "user:{$userId}:blocked_devices";
        $blocked = Cache::get($key, []);
        $blocked[] = $fingerprint;
        Cache::put($key, $blocked, now()->addDays(365));
    }
}
