<?php
declare(strict_types=1);

namespace App\ViewHelper\Container;

use App\Exception\ConfigurationError;
use App\ViewHelper\RepositoryInformation;
use Laminas\Diactoros\Uri;
use Psr\Container\ContainerInterface;

class RepositoryInformationFactory
{
    public function __invoke(ContainerInterface $container) : RepositoryInformation
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $url = $config['prismic']['api'] ?? null;

        if (empty($url)) {
            throw ConfigurationError::missingRepositoryUrl();
        }

        return new RepositoryInformation(new Uri($url));
    }
}
