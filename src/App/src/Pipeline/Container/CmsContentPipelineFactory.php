<?php
declare(strict_types=1);

namespace App\Pipeline\Container;

use App\Middleware\DocumentMeta;
use App\Middleware\ResolvedDocumentViewHelper;
use Laminas\Stratigility\MiddlewarePipeInterface;
use Mezzio\MiddlewareFactory;
use Primo\Middleware\DocumentResolver;
use Primo\Middleware\InjectRequestCookies;
use Primo\Middleware\PreviewCacheHeaders;
use Primo\Middleware\PrismicTemplate;
use Psr\Container\ContainerInterface;

class CmsContentPipelineFactory
{
    public function __invoke(ContainerInterface $container) : MiddlewarePipeInterface
    {
        $factory = $container->get(MiddlewareFactory::class);

        return $factory->pipeline([
            // Make sure the the Prismic Api has access to the request cookies
            InjectRequestCookies::class,
            // Set Cache headers if we are in preview mode
            PreviewCacheHeaders::class,
            // Routing should be done, so we should be able to resolve the current document
            DocumentResolver::class,
            // Provide the Resolved Document View Helper with the resolved document
            ResolvedDocumentViewHelper::class,
            // Apply metadata to the view for the resolved document
            DocumentMeta::class,
            // Render the view
            PrismicTemplate::class,
        ]);
    }
}
