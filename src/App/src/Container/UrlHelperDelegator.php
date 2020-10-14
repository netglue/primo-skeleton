<?php

declare(strict_types=1);

namespace App\Container;

use Mezzio\Helper\Exception\RuntimeException;
use Mezzio\Helper\UrlHelper;
use Mezzio\Router\RouterInterface;
use Psr\Container\ContainerInterface;

class UrlHelperDelegator
{
    public function __invoke(ContainerInterface $container, string $name, callable $target): UrlHelper
    {
        return new class ($container->get(RouterInterface::class)) extends UrlHelper {
            /** @inheritDoc */
            public function __invoke(
                ?string $routeName = null,
                array $routeParams = [],
                array $queryParams = [],
                ?string $fragmentIdentifier = null,
                array $options = []
            ): string {
                try {
                    return parent::__invoke($routeName, $routeParams, $queryParams, $fragmentIdentifier, $options);
                } catch (RuntimeException $exception) {
                    return '';
                }
            }
        };
    }
}
