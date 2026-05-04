<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Image Moderation Service
 * 
 * Suporta múltiplos providers:
 * - Google Vision API (Safe Search Detection)
 * - Sightengine (configurado no composer.json)
 * 
 * Configuração no .env:
 * IMAGE_MODERATION_PROVIDER=google|sightengine
 * GOOGLE_VISION_API_KEY=your_key
 * SIGHTENGINE_API_USER=user
 * SIGHTENGINE_API_SECRET=secret
 */
class ImageModerationService
{
    const ADULT_UNLIKELY = 'VERY_UNLIKELY';
    const ADULT_POSSIBLE = 'POSSIBLE';
    const ADULT_LIKELY = 'LIKELY';
    const ADULT_VERY_LIKELY = 'VERY_LIKELY';

    protected string $provider;
    protected array $config;

    public function __construct()
    {
        $this->provider = config('services.image_moderation.provider', 'google');
        $this->config = config('services.image_moderation');
    }

    /**
     * Analisa uma imagem por conteúdo inadequado
     * 
     * @param string $imagePath Caminho da imagem (storage ou URL)
     * @return array Resultado da análise
     */
    public function analyze(string $imagePath): array
    {
        return match ($this->provider) {
            'google' => $this->analyzeWithGoogleVision($imagePath),
            'sightengine' => $this->analyzeWithSightengine($imagePath),
            default => $this->analyzeWithGoogleVision($imagePath),
        };
    }

    /**
     * Verifica se a imagem é segura para publicação
     */
    public function isSafe(string $imagePath): bool
    {
        $result = $this->analyze($imagePath);
        
        return $result['is_safe'] ?? false;
    }

    /**
     * Análise com Google Vision API
     */
    protected function analyzeWithGoogleVision(string $imagePath): array
    {
        $apiKey = $this->config['google_api_key'];
        
        if (!$apiKey) {
            Log::warning('Google Vision API key not configured');
            return ['is_safe' => true, 'provider' => 'google', 'error' => 'API key missing'];
        }

        // Converte imagem para base64
        $imageData = $this->getImageBase64($imagePath);
        
        if (!$imageData) {
            return ['is_safe' => false, 'provider' => 'google', 'error' => 'Could not read image'];
        }

        $url = "https://vision.googleapis.com/v1/images:annotate?key={$apiKey}";

        $response = Http::timeout(30)->post($url, [
            'requests' => [
                [
                    'image' => [
                        'content' => $imageData,
                    ],
                    'features' => [
                        [
                            'type' => 'SAFE_SEARCH_DETECTION',
                            'maxResults' => 10,
                        ],
                        [
                            'type' => 'LABEL_DETECTION',
                            'maxResults' => 10,
                        ],
                    ],
                ],
            ],
        ]);

        if (!$response->successful()) {
            Log::error('Google Vision API error', ['response' => $response->body()]);
            return [
                'is_safe' => false,
                'provider' => 'google',
                'error' => 'API request failed',
                'details' => $response->json(),
            ];
        }

        $data = $response->json();
        $safeSearch = $data['responses'][0]['safeSearchAnnotation'] ?? [];

        // Análise de segurança
        $isSafe = $this->evaluateGoogleSafeSearch($safeSearch);

        return [
            'is_safe' => $isSafe,
            'provider' => 'google',
            'adult' => $safeSearch['adult'] ?? 'UNKNOWN',
            'violence' => $safeSearch['violence'] ?? 'UNKNOWN',
            'racy' => $safeSearch['racy'] ?? 'UNKNOWN',
            'spoof' => $safeSearch['spoof'] ?? 'UNKNOWN',
            'medical' => $safeSearch['medical'] ?? 'UNKNOWN',
            'labels' => $data['responses'][0]['labelAnnotations'] ?? [],
        ];
    }

    /**
     * Avalia resultado do Google Safe Search
     */
    protected function evaluateGoogleSafeSearch(array $safeSearch): bool
    {
        $forbidden = [self::ADULT_LIKELY, self::ADULT_VERY_LIKELY];
        
        // Bloqueia se adulto ou violência forem LIKELY ou VERY_LIKELY
        if (in_array($safeSearch['adult'] ?? '', $forbidden)) {
            return false;
        }
        
        if (in_array($safeSearch['violence'] ?? '', $forbidden)) {
            return false;
        }

        // Racy pode ser POSSIBLE dependendo do contexto
        if (($safeSearch['racy'] ?? '') === self::ADULT_VERY_LIKELY) {
            return false;
        }

        return true;
    }

    /**
     * Análise com Sightengine
     */
    protected function analyzeWithSightengine(string $imagePath): array
    {
        $apiUser = $this->config['sightengine_api_user'];
        $apiSecret = $this->config['sightengine_api_secret'];

        if (!$apiUser || !$apiSecret) {
            Log::warning('Sightengine API credentials not configured');
            return ['is_safe' => true, 'provider' => 'sightengine', 'error' => 'Credentials missing'];
        }

        $url = 'https://api.sightengine.com/1.0/check.json';

        $response = Http::timeout(30)->asForm()->post($url, [
            'api_user' => $apiUser,
            'api_secret' => $apiSecret,
            'url' => $this->getImageUrl($imagePath),
            'models' => 'nudity,wad,offensive,scam,face-attributes',
        ]);

        if (!$response->successful()) {
            Log::error('Sightengine API error', ['response' => $response->body()]);
            return [
                'is_safe' => false,
                'provider' => 'sightengine',
                'error' => 'API request failed',
            ];
        }

        $data = $response->json();

        // Sightengine retorna probabilidades
        $nuditySafe = ($data['nudity']['safe'] ?? 0) > 0.5;
        $weaponSafe = ($data['weapon']['classes']['firearm']['prob'] ?? 0) < 0.5;
        $drugsSafe = ($data['drugs']['prob'] ?? 0) < 0.5;
        $offensiveSafe = ($data['offensive']['prob'] ?? 0) < 0.5;

        $isSafe = $nuditySafe && $weaponSafe && $drugsSafe && $offensiveSafe;

        return [
            'is_safe' => $isSafe,
            'provider' => 'sightengine',
            'nudity' => $data['nudity'] ?? [],
            'weapon' => $data['weapon'] ?? [],
            'drugs' => $data['drugs'] ?? [],
            'offensive' => $data['offensive'] ?? [],
            'faces' => $data['faces'] ?? [],
        ];
    }

    /**
     * Converte imagem para base64
     */
    protected function getImageBase64(string $imagePath): ?string
    {
        try {
            if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
                $content = file_get_contents($imagePath);
            } else {
                $content = Storage::disk('public')->get($imagePath);
            }

            return base64_encode($content);
        } catch (\Exception $e) {
            Log::error('Failed to read image', ['path' => $imagePath, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Retorna URL pública da imagem
     */
    protected function getImageUrl(string $imagePath): string
    {
        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }

        return Storage::disk('public')->url($imagePath);
    }

    /**
     * Batch analysis de múltiplas imagens
     */
    public function analyzeBatch(array $imagePaths): array
    {
        $results = [];
        
        foreach ($imagePaths as $path) {
            $results[$path] = $this->analyze($path);
        }

        return $results;
    }
}
