<?php
declare(strict_types=1);

namespace App\Pipeline\Container;

use App\Middleware\DocumentMeta;
use App\Middleware\NotFoundDocumentLocator;
use Laminas\Stratigility\MiddlewarePipeInterface;
use Mezzio\MiddlewareFactory;
use Primo\Middleware\InjectRequestCookies;
use Primo\Middleware\PreviewCacheHeaders;
use Primo\Middleware\PrismicTemplate;
use Psr\Container\ContainerInterface;

class CmsNotFoundPipelineFactory
{
    public function __invoke(ContainerInterface $container) : MiddlewarePipeInterface
    {
        $factory = $container->get(MiddlewareFactory::class);

        return $factory->pipeline([
            // Make sure the the Prismic Api has access to the request cookies
            InjectRequestCookies::class,
            // Set Cache headers if we are in preview mode
            PreviewCacheHeaders::class,
            // Locate a 404 document if possible
            NotFoundDocumentLocator::class,
            // Apply metadata to the view for the resolved document
            DocumentMeta::class,
            // Render the view
            PrismicTemplate::class,
        ]);
    }
}
