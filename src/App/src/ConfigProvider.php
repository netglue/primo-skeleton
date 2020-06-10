<?php
declare(strict_types=1);

namespace App;

use Laminas;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Mezzio;
use Primo;
use Prismic;
use Psr;

class ConfigProvider
{
    /** @return mixed[] */
    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates' => $this->getTemplates(),
            'caches' => $this->laminasCaches(),
            'primo' => $this->primoConfig(),
            'view_helpers' => $this->viewHelpers(),
        ];
    }

    /** @return mixed[] */
    public function getDependencies() : array
    {
        return [
            'factories' => [
                Cache\PrismicCache::class => Laminas\Cache\Service\StorageCacheAbstractServiceFactory::class,
                Handler\PingHandler::class => InvokableFactory::class,
                Middleware\DocumentMeta::class => Middleware\Container\DocumentMetaFactory::class,
                Pipeline\CmsContentPipeline::class => Pipeline\Container\CmsContentPipelineFactory::class,
                Psr\Http\Client\ClientInterface::class => Http\HttpClientFactory::class,
            ],
            'aliases' => [
                // Opting-In to Hydrating Result Sets. Turns Prismic Document Types into objects that we recognise.
                Prismic\ResultSet\ResultSetFactory::class => Primo\ResultSet\HydratingResultSetFactory::class,
            ],
            'delegators' => [
                Cache\PrismicCache::class => [
                    Cache\Psr6Delegator::class,
                ],
                Mezzio\Application::class => [
                    PipelineAndRoutesDelegator::class,
                ],
                Primo\Http\PrismicHttpClient::class => [
                    Http\PrismicClientCachingDelegator::class,
                ],
            ],
        ];
    }

    /** @return mixed[] */
    public function getTemplates() : array
    {
        return [
            /**
             * Maps a slice type to a template name:
             */
            'slices' => [
                'prose' => 'slice::prose',
                'header-illustration' => 'slice::header-illustration',
                'prose-float' => 'slice::prose-float',
            ],
            'map' => [
                /** Page Templates */
                'cms::page' => __DIR__ . '/../templates/pages/cms-page.phtml',

                /** Slice Templates */
                'slice::prose' => __DIR__ . '/../templates/slices/prose.phtml',
                'slice::header-illustration' => __DIR__ . '/../templates/slices/illustration-header.phtml',
                'slice::prose-float' => __DIR__ . '/../templates/slices/prose-float.phtml',
            ],
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
                    'options' => ['namespace' => 'Prismic'],
                ],
            ],
        ];
    }

    /** @return mixed[] */
    private function primoConfig() : array
    {
        return [
            'typeMap' => [
                'default' => Primo\Content\Document::class,
                'map' => [Content\WebPage::class => 'page'],
            ],
        ];
    }

    /** @return mixed[] */
    private function viewHelpers() : array
    {
        return [
            'factories' => [
                ViewHelper\LinkResolver::class => ViewHelper\Container\LinkResolverFactory::class,
                ViewHelper\RepositoryInformation::class => ViewHelper\Container\RepositoryInformationFactory::class,
                ViewHelper\SliceZoneRenderer::class => ViewHelper\Container\SliceZoneRendererFactory::class,
            ],
            'aliases' => [
                'linkResolver' => ViewHelper\LinkResolver::class,
                'repositoryInformation' => ViewHelper\RepositoryInformation::class,
                'sliceZoneRenderer' => ViewHelper\SliceZoneRenderer::class,
            ],
        ];
    }
}
