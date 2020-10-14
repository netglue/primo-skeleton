<?php

declare(strict_types=1);

namespace App\Content\Container;

use App\Content\ErrorDocumentLocator;
use App\Content\SingleDocumentLocator;
use App\Exception\ConfigurationError;
use Psr\Container\ContainerInterface;

use function array_map;
use function get_class;
use function gettype;
use function is_object;
use function is_string;
use function sprintf;

class ErrorDocumentLocatorFactory
{
    public function __invoke(ContainerInterface $container): ErrorDocumentLocator
    {
        $config = $container->get('config');
        $options = $config['primo']['error'];

        return new ErrorDocumentLocator(
            $this->assertStringIsLocator($options['default'] ?? null, $container),
            array_map(function ($serviceId) use ($container): SingleDocumentLocator {
                return $this->assertStringIsLocator($serviceId, $container);
            }, $options['map'] ?? [])
        );
    }

    /** @param mixed $serviceId */
    private function assertStringIsLocator($serviceId, ContainerInterface $container): SingleDocumentLocator
    {
        if (! is_string($serviceId) || empty($serviceId)) {
            throw ConfigurationError::withMessage(
                'The default error document value and all status code mapping values must be non-empty strings'
            );
        }

        if (! $container->has($serviceId)) {
            throw ConfigurationError::withMessage(sprintf(
                'The document locator service with id "%s" is not available in the container',
                $serviceId
            ));
        }

        $locator = $container->get($serviceId);

        if (! $locator instanceof SingleDocumentLocator) {
            throw ConfigurationError::withMessage(sprintf(
                'The document locator service "%s" did not yield an the correct type from the container. Received: "%s"',
                $serviceId,
                is_object($locator) ? get_class($locator) : gettype($locator)
            ));
        }

        return $locator;
    }
}
