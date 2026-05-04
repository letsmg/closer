<?php

namespace App\Traits;

/**
 * Trait para Sanitização de Saída de Dados
 * 
 * Protege contra XSS sanitizando dados de saída com htmlspecialchars()
 * 
 * Uso: use SanitizesOutput no Controller
 *      return $this->sanitizeResponse($data);
 */
trait SanitizesOutput
{
    /**
     * Sanitiza resposta para saída segura
     * Converte caracteres especiais em entidades HTML
     */
    protected function sanitizeResponse(array $data): array
    {
        return $this->sanitizeArray($data);
    }

    /**
     * Sanitiza array recursivamente
     */
    protected function sanitizeArray(array $data): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            // Sanitiza a chave também (prevenção contra key XSS)
            $safeKey = $this->sanitizeString((string) $key);
            
            if (is_array($value)) {
                $sanitized[$safeKey] = $this->sanitizeArray($value);
            } elseif (is_string($value)) {
                $sanitized[$safeKey] = $this->sanitizeString($value);
            } else {
                $sanitized[$safeKey] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Sanitiza string com htmlspecialchars
     * 
     * @param string $text Texto a ser sanitizado
     * @param int $flags Flags do htmlspecialchars (default: ENT_QUOTES | ENT_SUBSTITUTE)
     * @return string Texto sanitizado
     */
    protected function sanitizeString(string $text, int $flags = ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5): string
    {
        // Não sanitiza strings vazias
        if (empty($text)) {
            return $text;
        }

        // Converte caracteres especiais em entidades HTML
        // ENT_QUOTES: Converte aspas simples e duplas
        // ENT_SUBSTITUTE: Substitui caracteres inválidos
        // ENT_HTML5: Usa entidades HTML5
        return htmlspecialchars($text, $flags, 'UTF-8', false);
    }

    /**
     * Sanitiza string específica para atributo HTML
     */
    protected function sanitizeForAttribute(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8', true);
    }

    /**
     * Sanitiza string para uso em JavaScript
     * Usado quando dados serão inseridos em templates JS
     */
    protected function sanitizeForJavaScript(string $text): string
    {
        $text = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8', true);
        
        // Escape adicional para caracteres perigosos em JS
        $dangerous = ['\\', '"', "'", "\n", "\r", '</', '<script'];
        $safe = ['\\\\', '\\"', "\\'", '\\n', '\\r', '<\\/', '<\\/script'];
        
        return str_replace($dangerous, $safe, $text);
    }

    /**
     * Cria resposta JSON segura (já sanitizada)
     */
    protected function safeJsonResponse(array $data, int $status = 200, array $headers = []): \Illuminate\Http\JsonResponse
    {
        $sanitized = $this->sanitizeResponse($data);
        
        // Adiciona headers de segurança
        $securityHeaders = [
            'X-Content-Type-Options' => 'nosniff',
            'X-XSS-Protection' => '1; mode=block',
        ];
        
        return response()->json($sanitized, $status, array_merge($securityHeaders, $headers));
    }
}
