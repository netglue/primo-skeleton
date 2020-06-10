<?php
declare(strict_types=1);

namespace App\ViewHelper\Container;

use App\ViewHelper\LinkResolver;
use Prismic\LinkResolver as CmsLinkResolver;
use Psr\Container\ContainerInterface;

class LinkResolverFactory
{
    public function __invoke(ContainerInterface $container) : LinkResolver
    {
        return new LinkResolver($container->get(CmsLinkResolver::class));
    }
}
