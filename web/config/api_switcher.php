<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Switcher Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for the API switcher that routes
    | requests between real API and mock responses based on criteria.
    |
    */
    
    // Base URL for the real API
    'real_api_base_url' => env('REAL_API_BASE_URL', 'https://api.example.com'),
    
    // Enable or disable the API switcher
    'enabled' => env('API_SWITCHER_ENABLED', true),
    
    // Default behavior when no matching criteria is found
    // Options: 'real', 'mock', 'pass'
    // 'pass' means it will pass the request through to the next middleware
    'default_behavior' => env('API_SWITCHER_DEFAULT', 'pass'),
    
    // Log API requests and responses
    'logging' => [
        'enabled' => env('API_SWITCHER_LOGGING', true),
        'include_request_body' => env('API_SWITCHER_LOG_REQUEST_BODY', false),
        'include_response_body' => env('API_SWITCHER_LOG_RESPONSE_BODY', false),
    ],
    
    // Cache settings for API responses
    'cache' => [
        'enabled' => env('API_SWITCHER_CACHE', false),
        'ttl' => env('API_SWITCHER_CACHE_TTL', 3600), // time in seconds
    ],
    
    // HTTP client settings for real API requests
    'http_client' => [
        'timeout' => env('API_SWITCHER_TIMEOUT', 30),
        'connect_timeout' => env('API_SWITCHER_CONNECT_TIMEOUT', 10),
        'retry' => env('API_SWITCHER_RETRY', 1),
        'retry_delay' => env('API_SWITCHER_RETRY_DELAY', 100),
    ],
    
    // Headers to be forwarded to real API
    'forward_headers' => [
        'Authorization',
        'Content-Type',
        'Accept',
        'X-Requested-With',
    ],
];
