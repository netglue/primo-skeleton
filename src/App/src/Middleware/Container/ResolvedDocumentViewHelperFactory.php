<?php

declare(strict_types=1);

namespace App\Middleware\Container;

use App\Middleware\ResolvedDocumentViewHelper;
use App\ViewHelper\ResolvedDocument;
use Laminas\View\HelperPluginManager;
use Psr\Container\ContainerInterface;

use function assert;

class ResolvedDocumentViewHelperFactory
{
    public function __invoke(ContainerInterface $container): ResolvedDocumentViewHelper
    {
        $helpers = $container->get(HelperPluginManager::class);
        assert($helpers instanceof ContainerInterface);

        return new ResolvedDocumentViewHelper($helpers->get(ResolvedDocument::class));
    }
}
