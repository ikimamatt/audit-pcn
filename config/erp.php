<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ERP Shared Secret
    |--------------------------------------------------------------------------
    |
    | Secret key yang digunakan untuk memvalidasi HMAC-SHA256 signature.
    | Nilai ini HARUS SAMA persis dengan yang digunakan di sisi ERP PLN.
    |
    */
    'shared_secret' => env('ERP_SHARED_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | ERP Allowed Domain
    |--------------------------------------------------------------------------
    |
    | Domain ERP yang diizinkan untuk mengirim request ke service ini.
    | Digunakan untuk validasi header X-ERP-Domain.
    |
    */
    'allowed_domain' => env('ERP_ALLOWED_DOMAIN', 'http://127.0.0.1:8000'),

    /*
    |--------------------------------------------------------------------------
    | Token Time To Live (TTL)
    |--------------------------------------------------------------------------
    |
    | Durasi validitas token dalam detik. Default: 3600 (1 jam).
    |
    */
    'token_ttl' => (int) env('ERP_TOKEN_TTL', 3600),

    /*
    |--------------------------------------------------------------------------
    | HTTP Timeout
    |--------------------------------------------------------------------------
    |
    | Timeout untuk HTTP request ke service lain (dalam detik).
    |
    */
    'timeout' => (int) env('ERP_API_SERVICE_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Gateway Security Configurations
    |--------------------------------------------------------------------------
    */
    'gateway_internal_key' => env('INTERNAL_SERVICE_KEY', ''),
    'gateway_jwt_secret' => env('JWT_SECRET', ''),
];