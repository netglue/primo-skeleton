<?php

declare(strict_types=1);

namespace AppTest\Integration;

use AppTest\Integration\Framework\TestCase;
use Laminas\View\HelperPluginManager;
use Psr\Container\ContainerInterface;

use function array_keys;
use function array_merge;
use function sprintf;

class LazyFactoryTest extends TestCase
{
    /** @return mixed[] */
    public function serviceProvider(): iterable
    {
        $container = $this->getContainer();
        $config = $container->get('config');

        $names = array_merge(
            array_keys($config['dependencies']['factories'] ?? []),
            array_keys($config['dependencies']['aliases'] ?? [])
        );

        foreach ($names as $name) {
            yield sprintf('Service: %s', $name) => [$container, $name];
        }

        $viewHelperPluginManager = $container->get(HelperPluginManager::class);
        $names = array_merge(
            array_keys($config['view_helpers']['factories'] ?? []),
            array_keys($config['view_helpers']['aliases'] ?? [])
        );

        foreach ($names as $name) {
            yield sprintf('View Helper: %s', $name) => [$viewHelperPluginManager, $name];
        }
    }

    /** @dataProvider serviceProvider */
    public function testServiceCanBeRetrieved(ContainerInterface $container, string $name): void
    {
        self::assertTrue($container->has($name));
        $container->get($name);
    }
}
