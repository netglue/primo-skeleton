<?php
declare(strict_types=1);

namespace App\Listener;

use Phly\EventDispatcher\LazyListener;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProviderInterface;
use Primo\Event\WebhookEvent;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

use function assert;

class ProviderDelegator
{
    public function __invoke(ContainerInterface $container, string $name, callable $target) : ListenerProviderInterface
    {
        $provider = $target();
        assert($provider instanceof AttachableListenerProviderInterface);
        $provider->listen(
            WebhookEvent::class,
            new LazyListener($container, WebhookEventListener::class)
        );

        return $provider;
    }
}
