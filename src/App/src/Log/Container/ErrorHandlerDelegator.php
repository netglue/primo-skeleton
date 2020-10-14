<?php

declare(strict_types=1);

namespace App\Log\Container;

use App\Log\ErrorHandlerLoggingListener;
use Laminas\Stratigility\Middleware\ErrorHandler;
use Psr\Container\ContainerInterface;

use function assert;

class ErrorHandlerDelegator
{
    public function __invoke(ContainerInterface $container, string $name, callable $callback): ErrorHandler
    {
        $handler = $callback();
        assert($handler instanceof ErrorHandler);
        $handler->attachListener($container->get(ErrorHandlerLoggingListener::class));

        return $handler;
    }
}
