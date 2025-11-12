<?php

return [
    'generation' => [
        'min_distance_between_systems' => 50.0, // Distance minimale entre systèmes
        'exploration_radius' => 200.0, // Rayon d'exploration par défaut
        'max_nearby_systems' => 10, // Nombre max de systèmes à générer lors d'une exploration
    ],

    'travel' => [
        'base_speed' => 1.0, // Base speed (units per hour)
        'speed_multiplier' => [
            'yellow_dwarf' => 1.0,
            'red_dwarf' => 0.8,
            'orange_dwarf' => 0.9,
            'red_giant' => 1.2,
            'blue_giant' => 1.5,
            'white_dwarf' => 0.7,
        ],
    ],
];

