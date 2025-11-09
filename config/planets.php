<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Planet Types Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for planet types with their probability weights and
    | characteristic distributions. Used by PlanetGeneratorService for
    | procedural planet generation.
    |
    */

    'types' => [
        'tellurique' => [
            'weight' => 40, // 40% probability
            'characteristics' => [
                'size' => [
                    'petite' => 20,
                    'moyenne' => 60,
                    'grande' => 20,
                ],
                'temperature' => [
                    'froide' => 20,
                    'tempérée' => 60,
                    'chaude' => 20,
                ],
                'atmosphere' => [
                    'respirable' => 70,
                    'toxique' => 25,
                    'inexistante' => 5,
                ],
                'terrain' => [
                    'rocheux' => 30,
                    'océanique' => 10,
                    'désertique' => 10,
                    'forestier' => 40,
                    'urbain' => 5,
                    'mixte' => 5,
                ],
                'resources' => [
                    'abondantes' => 40,
                    'modérées' => 50,
                    'rares' => 10,
                ],
            ],
        ],

        'gazeuse' => [
            'weight' => 25, // 25% probability
            'characteristics' => [
                'size' => [
                    'petite' => 0,
                    'moyenne' => 20,
                    'grande' => 80,
                ],
                'temperature' => [
                    'froide' => 30,
                    'tempérée' => 40,
                    'chaude' => 30,
                ],
                'atmosphere' => [
                    'respirable' => 0,
                    'toxique' => 90,
                    'inexistante' => 10,
                ],
                'terrain' => [
                    'rocheux' => 0,
                    'océanique' => 0,
                    'désertique' => 0,
                    'forestier' => 0,
                    'urbain' => 0,
                    'mixte' => 100, // Gaseous planets have mixed/cloudy terrain
                ],
                'resources' => [
                    'abondantes' => 30,
                    'modérées' => 50,
                    'rares' => 20,
                ],
            ],
        ],

        'glacée' => [
            'weight' => 15, // 15% probability
            'characteristics' => [
                'size' => [
                    'petite' => 30,
                    'moyenne' => 50,
                    'grande' => 20,
                ],
                'temperature' => [
                    'froide' => 80,
                    'tempérée' => 20,
                    'chaude' => 0,
                ],
                'atmosphere' => [
                    'respirable' => 20,
                    'toxique' => 30,
                    'inexistante' => 50,
                ],
                'terrain' => [
                    'rocheux' => 20,
                    'océanique' => 0,
                    'désertique' => 0,
                    'forestier' => 0,
                    'urbain' => 0,
                    'glacé' => 80,
                ],
                'resources' => [
                    'abondantes' => 10,
                    'modérées' => 30,
                    'rares' => 60,
                ],
            ],
        ],

        'désertique' => [
            'weight' => 10, // 10% probability
            'characteristics' => [
                'size' => [
                    'petite' => 40,
                    'moyenne' => 50,
                    'grande' => 10,
                ],
                'temperature' => [
                    'froide' => 10,
                    'tempérée' => 20,
                    'chaude' => 70,
                ],
                'atmosphere' => [
                    'respirable' => 30,
                    'toxique' => 40,
                    'inexistante' => 30,
                ],
                'terrain' => [
                    'rocheux' => 30,
                    'océanique' => 0,
                    'désertique' => 70,
                    'forestier' => 0,
                    'urbain' => 0,
                    'mixte' => 0,
                ],
                'resources' => [
                    'abondantes' => 10,
                    'modérées' => 30,
                    'rares' => 60,
                ],
            ],
        ],

        'océanique' => [
            'weight' => 10, // 10% probability
            'characteristics' => [
                'size' => [
                    'petite' => 10,
                    'moyenne' => 40,
                    'grande' => 50,
                ],
                'temperature' => [
                    'froide' => 20,
                    'tempérée' => 60,
                    'chaude' => 20,
                ],
                'atmosphere' => [
                    'respirable' => 70,
                    'toxique' => 20,
                    'inexistante' => 10,
                ],
                'terrain' => [
                    'rocheux' => 10,
                    'océanique' => 80,
                    'désertique' => 0,
                    'forestier' => 5,
                    'urbain' => 0,
                    'mixte' => 5,
                ],
                'resources' => [
                    'abondantes' => 50,
                    'modérées' => 40,
                    'rares' => 10,
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Planet Name Generation
    |--------------------------------------------------------------------------
    |
    | Configuration for generating planet names. Names are generated using
    | prefixes and suffixes to create unique planet identifiers.
    |
    */

    'name_prefixes' => [
        'Kepler',
        'Proxima',
        'Alpha',
        'Beta',
        'Gamma',
        'Delta',
        'Epsilon',
        'Zeta',
        'Eta',
        'Theta',
        'Iota',
        'Kappa',
        'Lambda',
        'Mu',
        'Nu',
        'Xi',
        'Omicron',
        'Pi',
        'Rho',
        'Sigma',
        'Tau',
        'Upsilon',
        'Phi',
        'Chi',
        'Psi',
        'Omega',
        'HD',
        'Gliese',
        'TRAPPIST',
        'TOI',
    ],

    'name_suffixes' => [
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
        'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',
        'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
        'y', 'z',
        '1', '2', '3', '4', '5', '6', '7', '8', '9',
    ],
];
