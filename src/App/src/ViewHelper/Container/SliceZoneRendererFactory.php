<?php
declare(strict_types=1);

namespace App\ViewHelper\Container;

use App\ViewHelper\SliceZoneRenderer;
use Laminas\View\Helper\Partial;
use Laminas\View\HelperPluginManager;
use Psr\Container\ContainerInterface;

use function assert;

class SliceZoneRendererFactory
{
    public function __invoke(ContainerInterface $container) : SliceZoneRenderer
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $map = $config['templates']['slices'] ?? [];
        $helpers = $container->get(HelperPluginManager::class);
        assert($helpers instanceof ContainerInterface);

        return new SliceZoneRenderer($helpers->get(Partial::class), $map);
    }
}
