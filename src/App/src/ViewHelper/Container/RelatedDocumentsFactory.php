<?php

declare(strict_types=1);

namespace App\ViewHelper\Container;

use App\ViewHelper\RelatedDocuments;
use Prismic\ApiClient;
use Psr\Container\ContainerInterface;

class RelatedDocumentsFactory
{
    public function __invoke(ContainerInterface $container): RelatedDocuments
    {
        return new RelatedDocuments($container->get(ApiClient::class));
    }
}
