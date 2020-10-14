<?php

declare(strict_types=1);

namespace App\Log;

use App\Exception\ConfigurationError;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class FileLoggerFactory
{
    public function __invoke(ContainerInterface $container): LoggerInterface
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $options = $config['logging'] ?? [];

        $name = $options['name'] ?? 'Primo';
        $path = $options['path'] ?? null;

        if (! $path) {
            throw ConfigurationError::withMessage('I cannot log to a file if logging.path has not been specified');
        }

        return new Logger($name, [new StreamHandler($path)]);
    }
}
