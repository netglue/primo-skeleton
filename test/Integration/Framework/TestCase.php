<?php

declare(strict_types=1);

namespace AppTest\Integration\Framework;

use AppTest\Unit\Framework\TestCase as UnitTestCase;
use Laminas\ServiceManager\ServiceManager;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TestCase extends UnitTestCase
{
    /** @var ContainerInterface|null */
    private $container;

    /** @var Application|null */
    private $application;

    protected function getContainer(): ContainerInterface
    {
        if (! $this->container) {
            $config = require __DIR__ . '/../../config/config.php';
            $dependencies = $config['dependencies'];
            $dependencies['services']['config'] = $config;

            $this->container = new ServiceManager($dependencies);
        }

        return $this->container;
    }

    protected function getApplication(): Application
    {
        if (! $this->application) {
            $container = $this->getContainer();
            $this->application = $container->get(Application::class);
            $factory = $container->get(MiddlewareFactory::class);
            (require __DIR__ . '/../../../config/pipeline.php')($this->application, $factory, $container);
            (require __DIR__ . '/../../../config/routes.php')($this->application, $factory, $container);
        }

        return $this->application;
    }

    protected function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->getApplication()->handle($request);
    }
}
