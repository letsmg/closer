<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Terms Version
    |--------------------------------------------------------------------------
    |
    | Bump this value whenever Terms of Service change.
    | Clients must accept the current version to continue.
    |
    */
    'version' => env('TERMS_VERSION', '2026-05-20'),

    /*
    |--------------------------------------------------------------------------
    | Privacy Policy Version
    |--------------------------------------------------------------------------
    |
    | Bump this value whenever Privacy Policy changes.
    | Clients must accept the current version to continue.
    |
    */
    'privacy_version' => env('PRIVACY_VERSION', '2026-05-20'),
];
