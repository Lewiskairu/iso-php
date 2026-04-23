<?php

declare(strict_types=1);

return [
    'app' => [
        'name'     => env('APP_NAME', 'ISO Compliance Hub'),
        'env'      => env('APP_ENV', 'production'),
        'debug'    => filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOL),
        'base_url' => env('APP_URL', ''),
    ],
    'db' => [
        'driver'       => env('DB_DRIVER', 'mysql'),
        'host'         => env('DB_HOST', 'localhost'),
        'port'         => env('DB_PORT', '3306'),
        'database'     => env('DB_NAME', 'iso_compliance_hub'),
        'username'     => env('DB_USER', 'root'),
        'password'     => env('DB_PASSWORD', ''),
        'charset'      => 'utf8mb4',
        'database_url' => env('DATABASE_URL'),
    ],
    'auth' => [
        'session_key' => 'auth_user',
    ],
];
