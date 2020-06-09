<?php
declare(strict_types=1);

namespace AppTest\Integration\Framework;

use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Psr\Container\ContainerInterface;

class TestCase extends PHPUnitTestCase
{
    /** @var ContainerInterface|null */
    private $container;

    protected function getContainer() : ContainerInterface
    {
        if ($this->container) {
            return $this->container;
        }

        $config = require __DIR__ . '/../../config/config.php';
        $dependencies = $config['dependencies'];
        $dependencies['services']['config'] = $config;

        return new ServiceManager($dependencies);
    }
}
