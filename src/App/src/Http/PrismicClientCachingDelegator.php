<?php

declare(strict_types=1);

namespace App\Http;

use App\Cache\PrismicCache;
use Http\Client\Common\Plugin\CachePlugin;
use Http\Client\Common\PluginClient;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\StreamFactoryInterface;

use function assert;

class PrismicClientCachingDelegator
{
    public function __invoke(ContainerInterface $container, string $name, callable $callback): ClientInterface
    {
        $psrClient = $callback();
        assert($psrClient instanceof ClientInterface);

        return new PluginClient(
            $psrClient,
            [
                new CachePlugin(
                    $container->get(PrismicCache::class),
                    $container->get(StreamFactoryInterface::class),
                    [
                        'default_ttl' => null,
                        'cache_lifetime' => null,
                        'respect_response_cache_directives' => [],
                    ]
                ),
            ]
        );
    }
}
