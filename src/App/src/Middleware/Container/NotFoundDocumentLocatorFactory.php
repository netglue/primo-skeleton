<?php

declare(strict_types=1);

namespace App\Middleware\Container;

use App\Exception\ConfigurationError;
use App\Middleware\NotFoundDocumentLocator;
use Primo\Middleware\PrismicTemplate;
use Psr\Container\ContainerInterface;

use function gettype;
use function is_scalar;
use function is_string;
use function sprintf;

class NotFoundDocumentLocatorFactory
{
    public function __invoke(ContainerInterface $container): NotFoundDocumentLocator
    {
        $config = $container->get('config');
        $options = $config['primo']['notFound'] ?? [];

        $finder = $options['finder'] ?? null;
        if (! is_string($finder) || empty($finder) || ! $container->has($finder)) {
            throw ConfigurationError::withMessage(sprintf(
                'In order to resolve a 404 document, I need a "finder" that can be retrieved from the container. The ' .
                'finder should be a string stored in config.primo.notFound.finder - I received %s',
                is_scalar($finder) ? (string) $finder : gettype($finder)
            ));
        }

        $template = $options['template'] ?? null;
        if (! $template) {
            throw ConfigurationError::withMessage(
                'A template name is required in order to render content managed 404 pages'
            );
        }

        return new NotFoundDocumentLocator(
            $container->get($finder),
            $template,
            $options['templateAttribute'] ?? PrismicTemplate::DEFAULT_TEMPLATE_ATTRIBUTE
        );
    }
}
