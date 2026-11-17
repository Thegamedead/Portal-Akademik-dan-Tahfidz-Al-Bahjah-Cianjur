<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Rate Limiting — Layer 1: Identity-based
    |--------------------------------------------------------------------------
    | Maksimum percobaan login per identitas (username/NIY/NIS) dalam satu
    | jendela waktu (decay_seconds). Berlaku per guard.
    */
    'identity_max_attempts' => env('AUTH_IDENTITY_MAX_ATTEMPTS', 5),
    'identity_decay_seconds' => env('AUTH_IDENTITY_DECAY_SECONDS', 60),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting — Layer 2: IP-based
    |--------------------------------------------------------------------------
    | Maksimum percobaan login per alamat IP dalam satu jendela waktu.
    | Berlaku lintas guard.
    */
    'ip_max_attempts' => env('AUTH_IP_MAX_ATTEMPTS', 20),
    'ip_decay_seconds' => env('AUTH_IP_DECAY_SECONDS', 60),

    /*
    |--------------------------------------------------------------------------
    | CAPTCHA
    |--------------------------------------------------------------------------
    | captcha_threshold: jumlah gagal login sebelum CAPTCHA muncul.
    | provider: 'hcaptcha' atau 'recaptcha'
    | site_key dan secret_key: diisi via .env untuk environment production.
    | Untuk environment lokal (Laragon), gunakan test key bawaan provider.
    */
    'captcha_threshold' => env('AUTH_CAPTCHA_THRESHOLD', 3),
    'captcha_provider' => env('AUTH_CAPTCHA_PROVIDER', 'hcaptcha'),
    'captcha_site_key' => env('HCAPTCHA_SITE_KEY', '10000000-ffff-ffff-ffff-000000000001'),
    'captcha_secret_key' => env('HCAPTCHA_SECRET_KEY', '0x0000000000000000000000000000000000000000'),
];
