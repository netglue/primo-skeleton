<?php
declare(strict_types=1);

namespace App\ViewHelper\Container;

use App\Content\SingleDocumentLocator;
use App\Exception\ConfigurationError;
use App\ViewHelper\SingleDocument;
use Psr\Container\ContainerInterface;

use function sprintf;

class SingleDocumentAbstractFactory
{
    public function __invoke(ContainerInterface $container, string $requestedName) : SingleDocument
    {
        $locator = $container->get($requestedName);
        if (! $locator instanceof SingleDocumentLocator) {
            throw ConfigurationError::withMessage(sprintf(
                'The service identifier %s does not resolve to a %s',
                $requestedName,
                SingleDocumentLocator::class
            ));
        }

        return new SingleDocument($locator);
    }
}
