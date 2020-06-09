<?php
declare(strict_types=1);

namespace App\Cache;

use Laminas\Cache\Psr\CacheItemPool\CacheItemPoolDecorator;
use Laminas\Cache\Storage\StorageInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;

use function assert;

class Psr6Delegator
{
    public function __invoke(ContainerInterface $container, string $serviceName, callable $callback) : CacheItemPoolInterface
    {
        $adapter = $callback();
        assert($adapter instanceof StorageInterface);

        return new CacheItemPoolDecorator($adapter);
    }
}
