<?php
declare(strict_types=1);

namespace App;

use App\Pipeline\CmsContentPipeline;
use Mezzio\Application;
use Primo\Router\RouteParams;
use Psr\Container\ContainerInterface;

use function sprintf;

class RouteProvider
{
    /** @var RouteParams */
    private $params;
    /** @var Application */
    private $application;

    public function __construct(Application $application, ContainerInterface $container)
    {
        $this->application = $application;
        $this->params = $container->get(RouteParams::class);
    }

    public function __invoke() : void
    {
        // Help FastRoute Limit matches for UID strings
        $uidConstraint = sprintf('{%s:[\w]+[\w-]+}', $this->params->uid());

        // The home page is "bookmarked"
        $home = $this->application->get('/', CmsContentPipeline::class, 'home');
        $home->setOptions([
            'defaults' => [
                'template' => 'cms::page',
                $this->params->bookmark() => 'home', // <- This bookmark is defined in your prismic repository settings
            ],
        ]);

        // All documents with the 'page' type that have been tagged with 'docs' will be available at /docs/{document-uid}
        $docs = $this->application->get(sprintf('/docs/%s', $uidConstraint), CmsContentPipeline::class, 'docs');
        $docs->setOptions([
            'defaults' => [
                'template' => 'cms::page',
                $this->params->type() => 'page',
                $this->params->tag() => 'docs',
            ],
        ]);

        // All documents with the 'page' type will be available at /{document-uid}
        $page = $this->application->get(sprintf('/%s', $uidConstraint), CmsContentPipeline::class, 'page');
        $page->setOptions([
            'defaults' => [
                'template' => 'cms::page',
                $this->params->type() => 'page',
            ],
        ]);

        $errorPreview = $this->application->get(sprintf('/error-preview/%s', $uidConstraint), CmsContentPipeline::class, 'error-preview');
        $errorPreview->setOptions([
            'defaults' => [
                'template' => 'cms::error',
                $this->params->type() => 'error',
            ],
        ]);
    }
}
