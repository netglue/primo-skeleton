<?php

declare(strict_types=1);

namespace App\Middleware\Container;

use App\Cache\PageCache;
use App\Exception\ConfigurationError;
use App\Middleware\CacheMiddleware;
use Psr\Container\ContainerInterface;

use function array_merge;
use function is_string;

class CacheMiddlewareFactory
{
    /** @var mixed[] */
    private static $defaultOptions = [
        'cache' => PageCache::class,
        'enabled' => true,
        'unCacheAbleRouteNames' => [],
    ];

    public function __invoke(ContainerInterface $container): CacheMiddleware
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $debug = (bool) ($config['debug'] ?: false);
        $options = $config['page_cache'] ?? [];

        // In development mode, with an absence of the enabled flag, turn caching off.
        if (! isset($options['enabled']) && $debug === true) {
            $options['enabled'] = false;
        }

        $options = array_merge(self::$defaultOptions, $options);

        if (! is_string($options['cache']) || ! $container->has($options['cache'])) {
            throw ConfigurationError::withMessage(
                'Either the cache service name is not a string, or it does not exist in the container'
            );
        }

        return new CacheMiddleware(
            $container->get($options['cache']),
            $options['unCacheAbleRouteNames'],
            $options['enabled'] ?? true
        );
    }
}
