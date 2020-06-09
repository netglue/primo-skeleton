<?php

declare(strict_types=1);

// Delegate static file requests back to the PHP built-in webserver
use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

if (PHP_SAPI === 'cli-server' && $_SERVER['SCRIPT_FILENAME'] !== __FILE__) {
    return false;
}

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

(static function () : void {
    $container = require __DIR__ . '/../config/container.php';
    assert($container instanceof ContainerInterface);

    $app = $container->get(Application::class);
    $factory = $container->get(MiddlewareFactory::class);
    (require __DIR__ . '/../config/pipeline.php')($app, $factory, $container);
    (require __DIR__ . '/../config/routes.php')($app, $factory, $container);

    $app->run();
})();
