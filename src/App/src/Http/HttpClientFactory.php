<?php
declare(strict_types=1);

namespace App\Http;

use Http\Client\Curl\Client as CurlClient;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class HttpClientFactory
{
    public function __invoke(ContainerInterface $container) : ClientInterface
    {
        return new CurlClient(
            $container->get(ResponseFactoryInterface::class),
            $container->get(StreamFactoryInterface::class)
        );
    }
}
