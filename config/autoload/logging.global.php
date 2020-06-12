<?php
declare(strict_types=1);

/**
 * The default system-wide logger is retrieved from the container with @link Psr\Log\LoggerInterface
 *
 * It is initially configured to log to a file in the `data` directory.
 *
 * To log to a service, rotating files, whatever, make a factory that returns a Psr Logger and alias the LoggerInterface
 * to your factory.
 */
return [
    'logging' => [
        'name' => 'PrimoSkeleton',
        'path' => __DIR__ . '/../../data/application.log',
    ],
    'dependencies' => [
        'factories' => [
            Psr\Log\LoggerInterface::class => App\Log\FileLoggerFactory::class,
        ],
    ],
];
