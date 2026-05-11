<?php

namespace App\Traits;

/**
 * Trait HasSanitization
 * 
 * Provides sanitization methods for input/output handling:
 * - strip_tags and trim on input
 * - htmlspecialchars on output
 */
trait HasSanitization
{
    /**
     * Sanitize a string input: strip tags and trim
     */
    public function sanitizeInput(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        
        $value = strip_tags($value);
        $value = trim($value);
        
        return $value === '' ? null : $value;
    }

    /**
     * Sanitize an array of string inputs
     */
    public function sanitizeArrayInput(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }
        
        return array_map(function ($value) {
            return is_string($value) ? $this->sanitizeInput($value) : $value;
        }, $values);
    }

    /**
     * Escape output for safe HTML display (htmlspecialchars)
     */
    public function escapeOutput(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
    }

    /**
     * Sanitize and validate a bio field (max 250 chars)
     */
    public function sanitizeBio(?string $bio): ?string
    {
        $bio = $this->sanitizeInput($bio);
        
        if ($bio !== null && mb_strlen($bio) > 250) {
            $bio = mb_substr($bio, 0, 250);
        }
        
        return $bio;
    }

    /**
     * Validate contact methods (max 3 apps from allowed list)
     */
    public function validateContactMethods(?array $methods): ?array
    {
        if ($methods === null || empty($methods)) {
            return null;
        }

        $allowedApps = [
            'whatsapp', 'telegram', 'discord', 'instagram', 'facebook',
            'signal', 'wechat', 'line', 'kakao', 'qq', 'weibo',
            'snapchat', 'tiktok', 'twitter', 'linkedin', 'email', 'phone'
        ];

        $validated = [];
        foreach ($methods as $method) {
            $app = $this->sanitizeInput($method['app'] ?? '');
            $value = $this->sanitizeInput($method['value'] ?? '');
            
            if (in_array(strtolower($app), $allowedApps) && !empty($value)) {
                $validated[] = [
                    'app' => strtolower($app),
                    'value' => $value,
                ];
            }
        }

        // Max 3 contact methods
        return array_slice($validated, 0, 3);
    }

    /**
     * Validate interests/hobbies (max 8)
     */
    public function validateInterests(?array $interestIds): ?array
    {
        if ($interestIds === null || empty($interestIds)) {
            return null;
        }

        // Ensure all are integers and max 8
        $validated = array_map('intval', $interestIds);
        $validated = array_unique($validated);
        
        return array_slice($validated, 0, 8);
    }
}