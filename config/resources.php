<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Optimal Resource Thresholds
    |--------------------------------------------------------------------------
    |
    | These values define the minimum number of approved resources needed
    | for each type to ensure sufficient diversity in the game.
    |
    */

    'optimal_thresholds' => [
        'avatar_image' => 50,   // Minimum 50 avatars for diversity (man/woman, styles)
        'planet_image' => 100,  // Minimum 100 planet images for diversity (types, characteristics)
        'planet_video' => 30,   // Minimum 30 planet videos (less needed than images)
    ],

    /*
    |--------------------------------------------------------------------------
    | Optimization Percentage Thresholds
    |--------------------------------------------------------------------------
    |
    | These values define the color coding for optimization status:
    | - Below 50%: Insufficient (red)
    | - 50-80%: In progress (orange)
    | - 80-100%: Optimal (green)
    | - Above 100%: Above threshold (blue)
    |
    */

    'optimization_thresholds' => [
        'insufficient' => 50,   // Below this: red
        'in_progress' => 80,     // Between insufficient and this: orange
        'optimal' => 100,        // Between in_progress and this: green
        // Above optimal: blue
    ],
];
