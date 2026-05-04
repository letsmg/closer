<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Moderation Services
    |--------------------------------------------------------------------------
    |
    | Configuração para APIs de moderação de imagem (Google Vision, Sightengine)
    |
    */
    'image_moderation' => [
        'provider' => env('IMAGE_MODERATION_PROVIDER', 'google'),
        
        // Google Vision API
        'google_api_key' => env('GOOGLE_VISION_API_KEY'),
        
        // Sightengine
        'sightengine_api_user' => env('SIGHTENGINE_API_USER'),
        'sightengine_api_secret' => env('SIGHTENGINE_API_SECRET'),
        
        // Thresholds (0-1, onde 1 = 100%)
        'thresholds' => [
            'adult' => env('IMAGE_MODERATION_ADULT_THRESHOLD', 0.7),
            'violence' => env('IMAGE_MODERATION_VIOLENCE_THRESHOLD', 0.7),
            'racy' => env('IMAGE_MODERATION_RACY_THRESHOLD', 0.8),
        ],
    ],

];
