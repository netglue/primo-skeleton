<?php
declare(strict_types=1);

namespace App;

use App\Pipeline\CmsContentPipeline;
use Mezzio\Application;
use Primo\Router\RouteParams;
use Psr\Container\ContainerInterface;

use function assert;
use function sprintf;

class PipelineAndRoutesDelegator
{
    public function __invoke(ContainerInterface $container, string $name, callable $callback) : Application
    {
        $application = $callback();
        assert($application instanceof Application);

        $params = $container->get(RouteParams::class);

        // The home page is "bookmarked"
        $home = $application->get('/', CmsContentPipeline::class, 'home');
        $home->setOptions([
            'defaults' => [
                'template' => 'cms::page',
                $params->bookmark() => 'home', // <- This bookmark is defined in your prismic repository settings
            ],
        ]);

        // All documents with the 'page' type will be available at /{document-uid}
        $page = $application->get(sprintf('/{%s}', $params->uid()), CmsContentPipeline::class, 'page');
        $page->setOptions([
            'defaults' => [
                'template' => 'cms::page',
                $params->type() => 'page',
            ],
        ]);

        $errorPreview = $application->get(sprintf('/error-preview/{%s}', $params->uid()), CmsContentPipeline::class, 'error-preview');
        $errorPreview->setOptions([
            'defaults' => [
                'template' => 'cms::error',
                $params->type() => 'error',
            ],
        ]);

        return $application;
    }
}
