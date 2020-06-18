<?php
declare(strict_types=1);

namespace App\Container;

use App\SearchService;
use Prismic\ApiClient;
use Psr\Container\ContainerInterface;

class SearchServiceFactory
{
    public function __invoke(ContainerInterface $container) : SearchService
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $seed = $config['primo']['site-search-defaults'] ?? [];

        return new SearchService(
            $container->get(ApiClient::class),
            ...$seed
        );
    }
}
