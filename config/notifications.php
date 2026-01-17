<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification Types
    |--------------------------------------------------------------------------
    |
    | Define the allowed notification types and their metadata.
    | Each type should have a description and expected data structure.
    |
    */

    'types' => [
        'message_important' => [
            'description' => 'Message important reçu dans la boîte mail Stellar (inbox)',
            'data_structure' => [
                'message_id' => 'string (ULID)',
                'inbox_url' => 'string (URL)',
            ],
        ],
        'ship_assigned' => [
            'description' => 'Vaisseau attribué',
            'data_structure' => [
                'ship_id' => 'string (ULID)',
                'ship_name' => 'string',
                'ship_url' => 'string (URL)',
            ],
        ],
        'resource_added' => [
            'description' => 'Nouvelles ressources ajoutées à l\'inventaire',
            'data_structure' => [
                'resource_type' => 'string',
                'quantity' => 'integer',
                'inventory_url' => 'string (URL)',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for notification behavior.
    |
    */

    'max_message_length' => 200, // Maximum length for notification message
    'default_limit' => 20, // Default limit for fetching notifications
    'dropdown_limit' => 10, // Limit for dropdown notifications
];
