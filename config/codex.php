<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Codex Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Codex Stellaris system, including validation
    | rules for planet names and content moderation.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Name Validation Rules
    |--------------------------------------------------------------------------
    |
    | Rules for validating planet names provided by users.
    |
    */

    'name_validation' => [
        'min_length' => 3,
        'max_length' => 50,
        'allowed_characters' => '/^[a-zA-Z0-9\s\-\']+$/u', // Letters, numbers, spaces, hyphens, apostrophes (with accents support)
        'forbidden_words' => [
            // Profanity and inappropriate words (case-insensitive)
            'admin',
            'administrator',
            'moderator',
            'mod',
            'test',
            'null',
            'undefined',
            'delete',
            'remove',
            'destroy',
            // Add more forbidden words as needed
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Validation Rules
    |--------------------------------------------------------------------------
    |
    | Rules for validating codex contributions and descriptions.
    |
    */

    'content_validation' => [
        'min_length' => 10,
        'max_length' => 5000,
        'forbidden_words' => [
            // Same as name validation, plus any content-specific words
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback Name Generation
    |--------------------------------------------------------------------------
    |
    | Configuration for generating fallback names when planets are not named.
    |
    */

    'fallback_name' => [
        'prefix' => 'Planète',
        'type_mapping' => [
            'tellurique' => 'Tellurique',
            'gazeuse' => 'Gazeuse',
            'glacee' => 'Glacée',
            'oceanique' => 'Océanique',
            'desertique' => 'Désertique',
            'volcanique' => 'Volcanique',
        ],
        'format' => '{prefix} {type} #{number}',
    ],
];

