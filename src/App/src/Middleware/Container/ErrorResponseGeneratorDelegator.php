<?php
declare(strict_types=1);

namespace App\Middleware\Container;

use App\Content\ErrorDocumentLocator;
use App\Middleware\ErrorResponseGenerator;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function assert;
use function is_callable;

class ErrorResponseGeneratorDelegator
{
    public function __invoke(ContainerInterface $container, string $name, callable $callback) : callable
    {
        $defaultResponseGenerator = $callback();
        assert(is_callable($defaultResponseGenerator));

        $config = $container->get('config');
        $debug = $config['debug'] ?? false;

        if ($debug === true) {
            return $defaultResponseGenerator;
        }

        $options = $config['primo']['error'];

        return new ErrorResponseGenerator(
            $container->get(TemplateRendererInterface::class),
            $options['template'],
            $container->get(ErrorDocumentLocator::class),
            $defaultResponseGenerator
        );
    }
}
