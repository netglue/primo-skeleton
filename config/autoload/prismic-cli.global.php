<?php
declare(strict_types=1);

return [
    'primo' => [
        'cli' => [
            'builder' => [
                'source' => __DIR__ . '/../../frontend/types/src',
                'dist' => __DIR__ . '/../../frontend/types/dist',
            ],
        ],
        'types' => [
            [
                'id' => 'page',
                'name' => 'Generic Web Page',
                'repeatable' => true,
            ],
            [
                'id' => 'error',
                'name' => 'Error Page',
                'repeatable' => true,
            ],
        ],
    ],
];
