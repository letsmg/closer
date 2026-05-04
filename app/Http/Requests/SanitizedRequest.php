<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request Base com Sanitização Automática
 * 
 * Todas as entradas são automaticamente sanitizadas com:
 * - strip_tags(): Remove tags HTML/PHP
 * - trim(): Remove espaços em branco
 * 
 * Uso: Extenda esta classe em vez de FormRequest
 */
abstract class SanitizedRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     * Sanitiza todas as strings antes da validação
     */
    protected function prepareForValidation(): void
    {
        $this->sanitizeInput($this->all());
    }

    /**
     * Sanitiza os dados de entrada recursivamente
     */
    protected function sanitizeInput(array $data): void
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            $sanitized[$key] = $this->sanitizeValue($value);
        }

        $this->replace($sanitized);
    }

    /**
     * Sanitiza um valor individual
     */
    protected function sanitizeValue(mixed $value): mixed
    {
        // Se for string, aplica sanitização
        if (is_string($value)) {
            // Remove tags HTML/PHP perigosas
            $value = strip_tags($value);
            
            // Remove espaços em branco no início e fim
            $value = trim($value);
            
            // Remove null bytes
            $value = str_replace("\0", '', $value);
            
            return $value;
        }

        // Se for array, sanitiza recursivamente
        if (is_array($value)) {
            return array_map([$this, 'sanitizeValue'], $value);
        }

        // Outros tipos retornam como estão
        return $value;
    }

    /**
     * Obtém input sanitizado
     */
    public function sanitized(string $key, mixed $default = null): mixed
    {
        return $this->sanitizeValue($this->input($key, $default));
    }
}
