<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Text Generation Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for AI text generation providers. Supports multiple
    | providers (OpenAI GPT, etc.) with their API endpoints and settings.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default Provider
    |--------------------------------------------------------------------------
    |
    | The default provider to use when generating text. Must be one of
    | the providers defined in the 'providers' array below.
    |
    */

    'default_provider' => env('TEXT_GENERATION_PROVIDER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | Providers Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for each text generation provider. Each provider must
    | have an 'api_key' (from environment), 'endpoint', and provider-specific
    | settings.
    |
    */

    'providers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'endpoint' => 'https://api.openai.com/v1/chat/completions',
            'model' => env('OPENAI_TEXT_MODEL', 'gpt-4o-mini'),
            'temperature' => env('OPENAI_TEXT_TEMPERATURE', 0.7),
            'max_tokens' => env('OPENAI_TEXT_MAX_TOKENS', 500),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Request Settings
    |--------------------------------------------------------------------------
    |
    | Global settings for text generation requests (timeout, retries, etc.)
    |
    */

    'timeout' => env('TEXT_GENERATION_TIMEOUT', 60), // seconds
    'retry_attempts' => env('TEXT_GENERATION_RETRY_ATTEMPTS', 3),
    'retry_delay' => env('TEXT_GENERATION_RETRY_DELAY', 2), // seconds

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for caching generated text descriptions to avoid
    | regenerating the same content.
    |
    */

    'cache' => [
        'enabled' => env('TEXT_GENERATION_CACHE_ENABLED', true),
        'ttl' => env('TEXT_GENERATION_CACHE_TTL', 86400), // 24 hours in seconds
        'prefix' => env('TEXT_GENERATION_CACHE_PREFIX', 'text_generation:'),
    ],
];

