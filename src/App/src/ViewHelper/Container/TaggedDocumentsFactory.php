<?php

declare(strict_types=1);

namespace App\ViewHelper\Container;

use App\ViewHelper\TaggedDocuments;
use Prismic\ApiClient;
use Psr\Container\ContainerInterface;

class TaggedDocumentsFactory
{
    public function __invoke(ContainerInterface $container): TaggedDocuments
    {
        return new TaggedDocuments($container->get(ApiClient::class));
    }
}
