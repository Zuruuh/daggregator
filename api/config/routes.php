<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $router): void {
    $root = dirname(__DIR__);

    $router->import(
        resource: "$root/src/Controller",
        type: 'attribute',
        ignoreErrors: false
    )->prefix('/');
};
