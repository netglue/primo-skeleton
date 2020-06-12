<?php
declare(strict_types=1);

namespace App\Listener\Container;

use App\Cache\PageCache;
use App\Cache\PrismicCache;
use App\Listener\WebhookEventListener;
use Prismic\ApiClient;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class WebhookEventListenerFactory
{
    public function __invoke(ContainerInterface $container) : WebhookEventListener
    {
        return new WebhookEventListener(
            $container->get(ApiClient::class),
            $container->get(LoggerInterface::class),
            $container->get(PrismicCache::class),
            $container->get(PageCache::class)
        );
    }
}
