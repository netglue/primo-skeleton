<?php
declare(strict_types=1);

use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

return static function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) : void {
    $app->get('/api/ping', App\Handler\PingHandler::class, 'api.ping');
    (new Primo\RouteProvider())($app, $container);
    (new App\RouteProvider($app, $container))();
};
