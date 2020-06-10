<?php
declare(strict_types=1);

return [
    'dependencies' => [
        'factories' => [
            Http\Mock\Client::class => static function () {
                return new Http\Mock\Client();
            },
        ],
        'aliases' => [
            Psr\Http\Client\ClientInterface::class => Http\Mock\Client::class,
        ],
    ],
];
