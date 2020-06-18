<?php
declare(strict_types=1);

namespace App\ViewHelper\Container;

use App\ViewHelper\GoogleAnalytics;
use Psr\Container\ContainerInterface;

class GoogleAnalyticsFactory
{
    public function __invoke(ContainerInterface $container) : GoogleAnalytics
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $id = $config['google-analytics'] ?? null;

        return new GoogleAnalytics($id);
    }
}
