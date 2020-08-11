<?php
declare(strict_types=1); // phpcs:ignoreFile

use Laminas\ConfigAggregator\ConfigAggregator;

return [
    'debug' => false,
    ConfigAggregator::ENABLE_CACHE => false,

    'dependencies' => [
        'factories' => [
            Http\Mock\Client::class => static function () {
                return new Http\Mock\Client();
            },
        ],
    ],
];
