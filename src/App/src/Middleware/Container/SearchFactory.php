<?php

declare(strict_types=1);

namespace App\Middleware\Container;

use App\Middleware\Search;
use App\SearchService;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class SearchFactory
{
    public function __invoke(ContainerInterface $container): Search
    {
        // Per-page could be made a routing parameter, or part of the Query, or a configuration item…
        $perPage = 10;

        return new Search(
            $container->get(TemplateRendererInterface::class),
            $container->get(SearchService::class),
            $perPage
        );
    }
}
