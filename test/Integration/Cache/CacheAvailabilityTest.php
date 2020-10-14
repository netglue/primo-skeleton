<?php

declare(strict_types=1);

namespace AppTest\Integration\Cache;

use App\Cache\PrismicCache;
use AppTest\Integration\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;

class CacheAvailabilityTest extends TestCase
{
    public function testPrismicCacheIsAvailable(): void
    {
        $container = $this->getContainer();
        $cache = $container->get(PrismicCache::class);
        $this->assertInstanceOf(CacheItemPoolInterface::class, $cache);
    }
}
