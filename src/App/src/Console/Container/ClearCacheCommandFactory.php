<?php
declare(strict_types=1);

namespace App\Console\Container;

use App\Cache\PageCache;
use App\Cache\PrismicCache;
use App\Console\ClearCacheCommand;
use Psr\Container\ContainerInterface;

class ClearCacheCommandFactory
{
    public function __invoke(ContainerInterface $container) : ClearCacheCommand
    {
        return new ClearCacheCommand(
            $container->get(PrismicCache::class),
            $container->get(PageCache::class)
        );
    }
}
