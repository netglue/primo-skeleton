<?php

declare(strict_types=1);

namespace AppTest\Integration\Http;

use AppTest\Integration\Framework\TestCase;
use Http\Client\Common\PluginClient;
use Primo\Http\PrismicHttpClient;

class PrismicClientCachingDelegatorTest extends TestCase
{
    public function testThatTheHttpClientIsWrappedWithPluginClient(): void
    {
        $container = $this->getContainer();
        $client = $container->get(PrismicHttpClient::class);
        $this->assertInstanceOf(PluginClient::class, $client);
    }
}
