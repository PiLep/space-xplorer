<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Video Generation Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for AI video generation providers. Supports multiple
    | providers (OpenAI Sora, RunwayML, Pika Labs, etc.) with their API endpoints
    | and settings.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default Provider
    |--------------------------------------------------------------------------
    |
    | The default provider to use when generating videos. Must be one of
    | the providers defined in the 'providers' array below.
    |
    */

    'default_provider' => env('VIDEO_GENERATION_PROVIDER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | Providers Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for each video generation provider. Each provider must
    | have an 'api_key' (from environment), 'endpoint', and provider-specific
    | settings.
    |
    */

    'providers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'endpoint' => env('OPENAI_VIDEO_ENDPOINT', 'https://api.openai.com/v1/videos'),
            'model' => env('OPENAI_VIDEO_MODEL', 'sora-2'), // sora-2 or sora-2-pro
            'size' => env('OPENAI_VIDEO_SIZE', '1280x720'), // e.g., 1280x720, 1920x1080
            'seconds' => env('OPENAI_VIDEO_SECONDS', 8), // Video duration in seconds
        ],

        'runway' => [
            'api_key' => env('RUNWAY_API_KEY'),
            'endpoint' => env('RUNWAY_ENDPOINT', 'https://api.runwayml.com/v1/generate'),
            'duration' => env('RUNWAY_DURATION', 5), // seconds
            'aspect_ratio' => env('RUNWAY_ASPECT_RATIO', '16:9'),
        ],

        'pika' => [
            'api_key' => env('PIKA_API_KEY'),
            'endpoint' => env('PIKA_ENDPOINT', 'https://api.pika.art/v1/generate'),
            'duration' => env('PIKA_DURATION', 4), // seconds
            'aspect_ratio' => env('PIKA_ASPECT_RATIO', '16:9'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Request Settings
    |--------------------------------------------------------------------------
    |
    | Global settings for video generation requests (timeout, retries, etc.)
    |
    */

    'timeout' => env('VIDEO_GENERATION_TIMEOUT', 300), // seconds (5 minutes for initial request)
    'retry_attempts' => env('VIDEO_GENERATION_RETRY_ATTEMPTS', 3),
    'retry_delay' => env('VIDEO_GENERATION_RETRY_DELAY', 5), // seconds

    /*
    |--------------------------------------------------------------------------
    | Polling Settings
    |--------------------------------------------------------------------------
    |
    | Settings for polling video generation job status (many providers use async jobs)
    |
    */

    'poll_max_attempts' => env('VIDEO_GENERATION_POLL_MAX_ATTEMPTS', 60), // Maximum polling attempts
    'poll_interval' => env('VIDEO_GENERATION_POLL_INTERVAL', 10), // seconds between polls (10-20 recommended by OpenAI)

    /*
    |--------------------------------------------------------------------------
    | Storage Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for storing generated videos. Videos are automatically
    | downloaded and saved to the configured storage disk.
    |
    */

    'storage' => [
        'disk' => env('VIDEO_GENERATION_STORAGE_DISK', 's3'), // s3, public, local
        'path' => env('VIDEO_GENERATION_STORAGE_PATH', 'videos/generated'), // Path within the disk
        'visibility' => env('VIDEO_GENERATION_STORAGE_VISIBILITY', 'public'), // public, private
    ],
];
