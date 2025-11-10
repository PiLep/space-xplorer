<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Image Generation Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for AI image generation providers. Supports multiple
    | providers (OpenAI DALL-E, Stability AI, etc.) with their API endpoints
    | and settings.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default Provider
    |--------------------------------------------------------------------------
    |
    | The default provider to use when generating images. Must be one of
    | the providers defined in the 'providers' array below.
    |
    */

    'default_provider' => env('IMAGE_GENERATION_PROVIDER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | Providers Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for each image generation provider. Each provider must
    | have an 'api_key' (from environment), 'endpoint', and provider-specific
    | settings.
    |
    */

    'providers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'endpoint' => 'https://api.openai.com/v1/images/generations',
            'model' => env('OPENAI_IMAGE_MODEL', 'dall-e-3'),
            'size' => env('OPENAI_IMAGE_SIZE', '1024x1024'), // 256x256, 512x512, 1024x1024, 1792x1024, 1024x1792
            'quality' => env('OPENAI_IMAGE_QUALITY', 'standard'), // standard, hd
            'style' => env('OPENAI_IMAGE_STYLE', 'vivid'), // vivid, natural
            'n' => 1, // Number of images to generate (DALL-E 3 only supports 1)
        ],

        'stability' => [
            'api_key' => env('STABILITY_AI_API_KEY'),
            'endpoint' => 'https://api.stability.ai/v1/generation/stable-diffusion-xl-1024-v1-0/text-to-image',
            'engine_id' => env('STABILITY_AI_ENGINE', 'stable-diffusion-xl-1024-v1-0'),
            'width' => env('STABILITY_AI_WIDTH', 1024),
            'height' => env('STABILITY_AI_HEIGHT', 1024),
            'steps' => env('STABILITY_AI_STEPS', 30),
            'cfg_scale' => env('STABILITY_AI_CFG_SCALE', 7),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Request Settings
    |--------------------------------------------------------------------------
    |
    | Global settings for image generation requests (timeout, retries, etc.)
    |
    */

    'timeout' => env('IMAGE_GENERATION_TIMEOUT', 60), // seconds
    'retry_attempts' => env('IMAGE_GENERATION_RETRY_ATTEMPTS', 3),
    'retry_delay' => env('IMAGE_GENERATION_RETRY_DELAY', 2), // seconds

    /*
    |--------------------------------------------------------------------------
    | Storage Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for storing generated images. Images are automatically
    | downloaded and saved to the configured storage disk.
    |
    */

    'storage' => [
        'disk' => env('IMAGE_GENERATION_STORAGE_DISK', 's3'), // s3, public, local
        'path' => env('IMAGE_GENERATION_STORAGE_PATH', 'images/generated'), // Path within the disk
        'visibility' => env('IMAGE_GENERATION_STORAGE_VISIBILITY', 'public'), // public, private
    ],
];
