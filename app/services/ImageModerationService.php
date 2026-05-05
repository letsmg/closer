<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * Service para Moderação de Imagens
 * 
 * Responsável por:
 * - Analisar imagens quanto a conteúdo impróprio
 * - Usar Google Vision API ou Sightengine
 * - Avaliar segurança de imagens
 * - Bloquear conteúdo inadequado
 */
class ImageModerationService
{
    private const UNSAFE_THRESHOLD = 0.6;
    private const ADULT_THRESHOLD = 0.5;
    private const VIOLENCE_THRESHOLD = 0.5;

    /**
     * Analisa imagemUploadedFile
     */
    public function analyzeImage(UploadedFile $file): array
    {
        $provider = config('services.image_moderation.provider', 'google');
        
        return match ($provider) {
            'google' => $this->analyzeWithGoogleVision($file),
            'sightengine' => $this->analyzeWithSightengine($file),
            default => $this->getDefaultResponse(),
        };
    }

    /**
     * Análise com Google Vision API
     */
    private function analyzeWithGoogleVision(UploadedFile $file): array
    {
        try {
            $apiKey = config('services.image_moderation.google_vision_api_key');
            
            if (!$apiKey) {
                throw new \Exception('Google Vision API key not configured');
            }

            // Prepara imagem para API
            $imageContent = base64_encode(file_get_contents($file->getPathname()));
            
            $response = Http::post("https://vision.googleapis.com/v1/images:annotate?key={$apiKey}", [
                'requests' => [
                    [
                        'image' => [
                            'content' => $imageContent,
                        ],
                        'features' => [
                            ['type' => 'SAFE_SEARCH_DETECTION'],
                            ['type' => 'LABEL_DETECTION'],
                            ['type' => 'WEB_DETECTION'],
                        ],
                    ],
                ],
            ]);

            if (!$response->successful()) {
                throw new \Exception('Google Vision API request failed: ' . $response->body());
            }

            $data = $response->json();
            $safeSearch = $data['responses'][0]['safeSearchAnnotation'] ?? [];
            $labels = $data['responses'][0]['labelAnnotations'] ?? [];

            return $this->processGoogleVisionResults($safeSearch, $labels);

        } catch (\Exception $e) {
            \Log::error('Google Vision analysis failed', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);

            return $this->getDefaultResponse();
        }
    }

    /**
     * Análise com Sightengine
     */
    private function analyzeWithSightengine(UploadedFile $file): array
    {
        try {
            $apiUser = config('services.image_moderation.sightengine_api_user');
            $apiSecret = config('services.image_moderation.sightengine_api_secret');

            if (!$apiUser || !$apiSecret) {
                throw new \Exception('Sightengine credentials not configured');
            }

            $response = Http::asForm()->post('https://api.sightengine.com/1.0/check.json', [
                'api_user' => $apiUser,
                'api_secret' => $apiSecret,
                'media' => $file->getPathname(),
                'models' => 'nudity,wad,generic,face,properties,text',
            ]);

            if (!$response->successful()) {
                throw new \Exception('Sightengine API request failed: ' . $response->body());
            }

            $data = $response->json();

            return $this->processSightengineResults($data);

        } catch (\Exception $e) {
            \Log::error('Sightengine analysis failed', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);

            return $this->getDefaultResponse();
        }
    }

    /**
     * Processa resultados do Google Vision
     */
    private function processGoogleVisionResults(array $safeSearch, array $labels): array
    {
        $adult = $this->convertGoogleLikelihood($safeSearch['adult'] ?? 'UNKNOWN');
        $violence = $this->convertGoogleLikelihood($safeSearch['violence'] ?? 'UNKNOWN');
        $racy = $this->convertGoogleLikelihood($safeSearch['racy'] ?? 'UNKNOWN');

        $isUnsafe = $adult >= self::ADULT_THRESHOLD || 
                   $violence >= self::VIOLENCE_THRESHOLD || 
                   $racy >= self::UNSAFE_THRESHOLD;

        return [
            'is_safe' => !$isUnsafe,
            'adult_probability' => $adult,
            'violence_probability' => $violence,
            'racy_probability' => $racy,
            'labels' => $this->extractLabels($labels),
            'provider' => 'google_vision',
            'analysis_time' => now()->toISOString(),
        ];
    }

    /**
     * Processa resultados do Sightengine
     */
    private function processSightengineResults(array $data): array
    {
        $nudity = $data['nudity'] ?? [];
        $weapon = $data['weapon'] ?? 0.0;
        $alcohol = $data['alcohol'] ?? 0.0;
        $drug = $data['drug'] ?? 0.0;

        $adultProbability = max(
            $nudity['partial'] ?? 0.0,
            $nudity['sexual_activity'] ?? 0.0,
            $nudity['sexual_display'] ?? 0.0
        );

        $violenceProbability = max($weapon, $alcohol, $drug);

        $isUnsafe = $adultProbability >= self::ADULT_THRESHOLD || 
                   $violenceProbability >= self::VIOLENCE_THRESHOLD;

        return [
            'is_safe' => !$isUnsafe,
            'adult_probability' => $adultProbability,
            'violence_probability' => $violenceProbability,
            'racy_probability' => $nudity['suggestive'] ?? 0.0,
            'labels' => $this->extractSightengineLabels($data),
            'provider' => 'sightengine',
            'analysis_time' => now()->toISOString(),
        ];
    }

    /**
     * Converte likelihood do Google para probabilidade numérica
     */
    private function convertGoogleLikelihood(string $likelihood): float
    {
        return match ($likelihood) {
            'VERY_UNLIKELY' => 0.1,
            'UNLIKELY' => 0.2,
            'POSSIBLE' => 0.5,
            'LIKELY' => 0.7,
            'VERY_LIKELY' => 0.9,
            'UNKNOWN' => 0.0,
            default => 0.0,
        };
    }

    /**
     * Extrai labels do Google Vision
     */
    private function extractLabels(array $labels): array
    {
        return array_map(fn($label) => [
            'name' => $label['description'] ?? '',
            'confidence' => $label['score'] ?? 0.0,
        ], array_slice($labels, 0, 10));
    }

    /**
     * Extrai labels do Sightengine
     */
    private function extractSightengineLabels(array $data): array
    {
        $labels = [];

        if (isset($data['face'])) {
            $labels[] = ['name' => 'face_detected', 'confidence' => $data['face']['confidence'] ?? 0.0];
        }

        if (isset($data['text'])) {
            $labels[] = ['name' => 'text_detected', 'confidence' => $data['text']['confidence'] ?? 0.0];
        }

        return $labels;
    }

    /**
     * Resposta padrão quando APIs não estão disponíveis
     */
    private function getDefaultResponse(): array
    {
        return [
            'is_safe' => true, // Por segurança, aprova se não conseguir analisar
            'adult_probability' => 0.0,
            'violence_probability' => 0.0,
            'racy_probability' => 0.0,
            'labels' => [],
            'provider' => 'fallback',
            'analysis_time' => now()->toISOString(),
            'warning' => 'Analysis service unavailable - auto-approved',
        ];
    }

    /**
     * Verifica se imagem é segura baseada nos resultados
     */
    public function isImageSafe(array $analysis): bool
    {
        return $analysis['is_safe'] ?? true;
    }

    /**
     * Salva imagem se for segura
     */
    public function saveIfSafe(UploadedFile $file, string $path): ?string
    {
        $analysis = $this->analyzeImage($file);

        if (!$this->isImageSafe($analysis)) {
            \Log::warning('Image rejected due to safety concerns', [
                'file' => $file->getClientOriginalName(),
                'analysis' => $analysis,
            ]);
            return null;
        }

        return Storage::disk('public')->putFile($path, $file);
    }

    /**
     * Obtém estatísticas de moderação
     */
    public function getModerationStats(): array
    {
        // Em produção, buscar do banco ou cache
        return [
            'total_analyzed' => 0,
            'approved' => 0,
            'rejected' => 0,
            'rejection_reasons' => [
                'adult_content' => 0,
                'violence' => 0,
                'racy_content' => 0,
            ],
        ];
    }
}
