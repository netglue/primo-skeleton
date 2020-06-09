<?php
declare(strict_types=1);

namespace App;

use Laminas;
use Laminas\ServiceManager\Factory\InvokableFactory;

class ConfigProvider
{
    /** @return mixed[] */
    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates' => $this->getTemplates(),
            'caches' => $this->laminasCaches(),
        ];
    }

    /** @return mixed[] */
    public function getDependencies() : array
    {
        return [
            'factories' => [
                Handler\PingHandler::class => InvokableFactory::class,
                Cache\PrismicCache::class => Laminas\Cache\Service\StorageCacheAbstractServiceFactory::class,
            ],
            'delegators' => [
                Cache\PrismicCache::class => [
                    Cache\Psr6Delegator::class,
                ],
            ],
        ];
    }

    /** @return mixed[] */
    public function getTemplates() : array
    {
        return [
            'paths' => [
                'app'    => [__DIR__ . '/../templates/app'],
                'error'  => [__DIR__ . '/../templates/error'],
                'layout' => [__DIR__ . '/../templates/layout'],
            ],
        ];
    }

    /** @return mixed[] */
    private function laminasCaches() : array
    {
        return [
            Cache\PrismicCache::class => [
                'adapter' => [
                    'name' => Laminas\Cache\Storage\Adapter\Apcu::class,
                    'options' => [
                        'namespace' => 'Prismic',
                    ],
                ],
            ],
        ];
    }
}
