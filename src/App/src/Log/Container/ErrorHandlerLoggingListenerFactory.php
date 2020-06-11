<?php
declare(strict_types=1);

namespace App\Log\Container;

use App\Log\ErrorHandlerLoggingListener;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class ErrorHandlerLoggingListenerFactory
{
    public function __invoke(ContainerInterface $container) : ErrorHandlerLoggingListener
    {
        return new ErrorHandlerLoggingListener($container->get(LoggerInterface::class));
    }
}
