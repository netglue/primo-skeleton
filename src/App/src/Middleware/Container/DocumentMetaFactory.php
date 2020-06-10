<?php
declare(strict_types=1);

namespace App\Middleware\Container;

use App\Middleware\DocumentMeta;
use Laminas\View\Helper\HeadMeta;
use Laminas\View\Helper\HeadTitle;
use Laminas\View\HelperPluginManager;
use Psr\Container\ContainerInterface;

use function assert;

class DocumentMetaFactory
{
    public function __invoke(ContainerInterface $container) : DocumentMeta
    {
        $viewHelpers = $container->get(HelperPluginManager::class);
        assert($viewHelpers instanceof ContainerInterface);

        return new DocumentMeta(
            $viewHelpers->get(HeadTitle::class),
            $viewHelpers->get(HeadMeta::class)
        );
    }
}
