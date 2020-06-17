<?php
declare(strict_types=1);

namespace App\Pipeline\Container;

use App\Middleware\DocumentMeta;
use App\Middleware\NotFoundDocumentLocator;
use App\Middleware\ResolvedDocumentViewHelper;
use Laminas\Stratigility\MiddlewarePipeInterface;
use Mezzio\MiddlewareFactory;
use Primo\Middleware\PrismicTemplate;
use Psr\Container\ContainerInterface;

class CmsNotFoundPipelineFactory
{
    public function __invoke(ContainerInterface $container) : MiddlewarePipeInterface
    {
        $factory = $container->get(MiddlewareFactory::class);

        return $factory->pipeline([
            // Locate a 404 document if possible
            NotFoundDocumentLocator::class,
            // Provide the Resolved Document View Helper with the resolved error document
            ResolvedDocumentViewHelper::class,
            // Apply metadata to the view for the resolved document
            DocumentMeta::class,
            // Render the view
            PrismicTemplate::class,
        ]);
    }
}
