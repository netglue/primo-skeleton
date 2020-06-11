<?php
declare(strict_types=1);

namespace App\Middleware\Container;

use App\Content\ErrorDocumentLocator;
use App\Middleware\ErrorResponseGenerator;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class ErrorResponseGeneratorFactory
{
    public function __invoke(ContainerInterface $container) : ErrorResponseGenerator
    {
        $config = $container->get('config');
        $options = $config['primo']['error'];

        return new ErrorResponseGenerator(
            $container->get(TemplateRendererInterface::class),
            $options['template'],
            $container->get(ErrorDocumentLocator::class),
            $container->get($options['fallbackResponseGenerator'])
        );
    }
}
