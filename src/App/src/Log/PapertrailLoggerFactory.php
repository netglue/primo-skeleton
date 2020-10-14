<?php

declare(strict_types=1);

namespace App\Log;

use App\Exception\ConfigurationError;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;

class PapertrailLoggerFactory
{
    public function __invoke(ContainerInterface $container): Logger
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $options = $config['logging'] ?? [];

        $name = $options['name'] ?? 'Primo';
        $port = $options['papertrail']['port'] ?? null;
        $host = $options['papertrail']['host'] ?? null;

        if (! $port) {
            throw ConfigurationError::withMessage('The Papertrail logger requires a port number to be defined in logging.papertrail.port');
        }

        if (! $host) {
            throw ConfigurationError::withMessage('The Papertrail logger requires a host name to be defined in logging.papertrail.host');
        }

        return new Logger($name, [new SyslogUdpHandler($host, $port)]);
    }
}
