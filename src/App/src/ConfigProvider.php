<?php
declare(strict_types=1);

namespace App;

use Laminas;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Mezzio;
use Phly;
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
            'laminas-cli' => [
                'commands' => $this->consoleCommands(),
            ],
            'console' => [
                'commands' => $this->consoleCommands(),
            ],
        ];
    }

    /** @return mixed[] */
    public function getDependencies() : array
    {
        return [
            'factories' => [
                Cache\PrismicCache::class => Laminas\Cache\Service\StorageCacheAbstractServiceFactory::class,
                Console\ClearCacheCommand::class => Console\Container\ClearCacheCommandFactory::class,
                Content\ErrorDocumentLocator::class => Content\Container\ErrorDocumentLocatorFactory::class,
                Handler\PingHandler::class => InvokableFactory::class,
                Listener\WebhookEventListener::class => Listener\Container\WebhookEventListenerFactory::class,
                Log\ErrorHandlerLoggingListener::class => Log\Container\ErrorHandlerLoggingListenerFactory::class,
                Middleware\CacheMiddleware::class => Middleware\Container\CacheMiddlewareFactory::class,
                Middleware\DocumentMeta::class => Middleware\Container\DocumentMetaFactory::class,
                Middleware\NotFoundDocumentLocator::class => Middleware\Container\NotFoundDocumentLocatorFactory::class,
                Pipeline\CmsContentPipeline::class => Pipeline\Container\CmsContentPipelineFactory::class,
                Pipeline\CmsNotFoundPipeline::class => Pipeline\Container\CmsNotFoundPipelineFactory::class,
                Psr\Http\Client\ClientInterface::class => Http\HttpClientFactory::class,
                Psr\Log\LoggerInterface::class => Log\FileLoggerFactory::class,
                'DefaultMezzioErrorResponseGenerator' => Mezzio\Container\ErrorResponseGeneratorFactory::class,
            ],
            'aliases' => [
                // Opting-In to Hydrating Result Sets. Turns Prismic Document Types into objects that we recognise.
                Prismic\ResultSet\ResultSetFactory::class => Primo\ResultSet\HydratingResultSetFactory::class,
                Psr\EventDispatcher\EventDispatcherInterface::class => Phly\EventDispatcher\ErrorEmittingDispatcher::class,
                Psr\EventDispatcher\ListenerProviderInterface::class => Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider::class,
            ],
            'delegators' => [
                Cache\PrismicCache::class => [
                    Cache\Psr6Delegator::class,
                ],
                Cache\PageCache::class => [
                    Cache\Psr6Delegator::class,
                ],
                Laminas\Stratigility\Middleware\ErrorHandler::class => [
                    Log\Container\ErrorHandlerDelegator::class,
                ],
                Mezzio\Helper\UrlHelper::class => [
                    Container\UrlHelperDelegator::class,
                ],
                Mezzio\Middleware\ErrorResponseGenerator::class => [
                    Middleware\Container\ErrorResponseGeneratorDelegator::class,
                ],
                Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider::class => [
                    Listener\ProviderDelegator::class,
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
                'code' => 'slice::code',
            ],
            'map' => [
                /** Page Templates */
                'cms::page' => __DIR__ . '/../templates/pages/cms-page.phtml',
                'cms::error' => __DIR__ . '/../templates/pages/cms-error.phtml',

                /** Slice Templates */
                'slice::prose' => __DIR__ . '/../templates/slices/prose.phtml',
                'slice::header-illustration' => __DIR__ . '/../templates/slices/illustration-header.phtml',
                'slice::prose-float' => __DIR__ . '/../templates/slices/prose-float.phtml',
                'slice::code' => __DIR__ . '/../templates/slices/code.phtml',
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
            Cache\PageCache::class => [
                'adapter' => [
                    'name' => Laminas\Cache\Storage\Adapter\Apcu::class,
                    'options' => ['namespace' => 'PrimoHttp'],
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
                'map' => [
                    Content\WebPage::class => 'page',
                    Content\ErrorPage::class => 'error',
                ],
            ],
            'notFound' => [
                'finder' => null, // <- This will fail by default. We have no idea how to find your 404 document yet
                'template' => 'cms::error', // <- The template to render the 404 page with.
                'templateAttribute' => Primo\Middleware\PrismicTemplate::DEFAULT_TEMPLATE_ATTRIBUTE, // <- It's unlikely you'll want to change this
            ],
            'error' => [
                'template' => 'cms::error', // <- Template for errors
                'default' => null, // <- Must be a string that can return a SingleDocumentLocator from the container
                /**
                 * An array where keys are HTTP Status codes and values are strings that return
                 * SingleDocumentLocator instances from the container.
                 *
                 * The array can be empty. In this case, the default error will always be used.
                 *
                 * It is useful however if you throw a 403 exception and want a custom 'You’re not allowed to see this' page
                 */
                'map' => [],
            ],
            /**
             * This array contains 'special' documents that you want to be able to retrieve and inject easily into
             * various places. A good example is a 404 document. You decide the identifier, and the value is an array
             * that can be fed to the @link Content\Container\SingleDocumentLocatorStaticFactory
             *
             * Some examples are documented inline:
             */
            // phpcs:ignore
            'documents' => [
                // // Find a single document by bookmark
                // 'document.404' => [
                //     'bookmark' => 'error404',
                // ],
                // // Find a single document by uid
                // 'document.special' => [
                //     'type' => 'some-prismic-type',
                //     'uid' => 'some-uid',
                // ],
                // // Find a single document by type (Useful for non-repeatable types)
                // 'document.single' => [
                //     'type' => 'a-single-type',
                // ],
                // // Find a document using Predicates
                // 'document.something-else' => [
                //     'predicates' => [
                //         Prismic\Predicate::at('document.type', 'my-type'),
                //         Prismic\Predicate::at('my.my-type.error-code', 403),
                //     ],
                // ],
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

    /** @return mixed[] */
    private function consoleCommands() : array
    {
        return [
            Console\ClearCacheCommand::DEFAULT_NAME => Console\ClearCacheCommand::class,
        ];
    }
}
