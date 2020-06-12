<?php
declare(strict_types=1);

namespace App\Listener;

use Primo\Event\WebhookEvent;
use Prismic\ApiClient;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use function sprintf;

/**
 * Cache-Busting Web Hook Listener
 *
 * This listener is dumb. It just busts the cache whenever it receives a payload that looks like
 * an "api-update" where the master ref has changed and pops some info into the logger.
 *
 * It is likely that this listener is insufficient for most needs and indiscriminate in when the cache should or should
 * not be cleared. That said, any logic here is dependent on how you have setup webhooks on the remote so it is
 * inappropriate to try and do anything clever by default.
 *
 * By consulting the docs at https://user-guides.prismic.io/en/articles/790505-webhooks you get see the shape of the
 * possible payloads and react accordingly.
 */
class WebhookEventListener
{
    /** @var CacheItemPoolInterface[] */
    private $pools;
    /** @var ApiClient */
    private $apiClient;
    /** @var LoggerInterface */
    private $logger;

    public function __construct(ApiClient $apiClient, LoggerInterface $logger, CacheItemPoolInterface ...$pools)
    {
        $this->pools = $pools;
        $this->apiClient = $apiClient;
        $this->logger = $logger;
    }

    public function __invoke(WebhookEvent $event) : void
    {
        $payload = $event->payload();

        $type = $payload->type ?? null;

        if ($type !== 'api-update') {
            return;
        }

        $currentRef = $this->apiClient->data()->master()->ref();
        $ref = $payload->masterRef ?? null;
        if (! $ref || $ref === $currentRef) {
            $this->logger->info(sprintf(
                'Received a webhook for the prismic repository "%s" but it does not look like the master ref ' .
                'has changed, so I’m leaving the cache intact.',
                $this->apiClient->host()
            ), ['payload' => $payload]);

            return;
        }

        $this->logger->info(sprintf(
            'The prismic repository "%s" master ref has changed from "%s" to "%s". Busting the caches…',
            $this->apiClient->host(),
            $currentRef,
            $ref
        ), ['payload' => $payload]);

        $this->clearCaches();
    }

    private function clearCaches() : void
    {
        foreach ($this->pools as $pool) {
            $pool->clear();
        }
    }
}
