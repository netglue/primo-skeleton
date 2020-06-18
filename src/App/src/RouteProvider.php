<?php
declare(strict_types=1);

namespace App;

use App\Middleware\Search;
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
        $langConstraint = sprintf('{%s:[a-z]{2}-[a-z]{2}}', $this->params->lang());

        // The home page is "bookmarked"
        $home = $this->application->get('/', CmsContentPipeline::class, 'home');
        $home->setOptions([
            'defaults' => [
                'template' => 'cms::page',
                $this->params->bookmark() => 'home', // <- This bookmark is defined in your prismic repository settings
            ],
        ]);

        // All documents with the 'page' type that have been tagged with 'docs' will be available at /{language}/docs/{document-uid}
        $docs = $this->application->get(sprintf('/%s/docs/%s', $langConstraint, $uidConstraint), CmsContentPipeline::class, 'docs');
        $docs->setOptions([
            'defaults' => [
                'template' => 'cms::page',
                $this->params->type() => 'page',
                $this->params->tag() => 'docs',
            ],
        ]);

        // All documents with the 'page' type will be available at /{language}/{document-uid}
        $page = $this->application->get(sprintf('/%s/%s', $langConstraint, $uidConstraint), CmsContentPipeline::class, 'page');
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

        $searchResults = $this->application->get('/search[/{page:[0-9]+}]', Search::class, 'search');
        $searchResults->setOptions([
            'defaults' => [
                'template' => 'static::search-results',
                'page' => 1,
            ],
        ]);
    }
}
