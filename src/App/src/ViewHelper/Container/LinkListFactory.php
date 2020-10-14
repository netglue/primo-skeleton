<?php

declare(strict_types=1);

namespace App\ViewHelper\Container;

use App\ViewHelper\LinkList;
use Laminas\Escaper\Escaper;
use Prismic\LinkResolver;
use Psr\Container\ContainerInterface;

class LinkListFactory
{
    public function __invoke(ContainerInterface $container): LinkList
    {
        return new LinkList(
            $container->get(Escaper::class),
            $container->get(LinkResolver::class)
        );
    }
}
