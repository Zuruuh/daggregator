<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Http\Discovery\Psr17Factory;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

const HTTP_DISCOVERY_SERVICE_ID = 'http_discovery.psr17_factory';

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $root = dirname(__DIR__, 2);

    $services(HTTP_DISCOVERY_SERVICE_ID)->class(Psr17Factory::class);
    $services->alias(RequestFactoryInterface::class, HTTP_DISCOVERY_SERVICE_ID);
    $services->alias(ResponseFactoryInterface::class, HTTP_DISCOVERY_SERVICE_ID);
    $services->alias(ServerRequestFactoryInterface::class, HTTP_DISCOVERY_SERVICE_ID);
    $services->alias(StreamFactoryInterface::class, HTTP_DISCOVERY_SERVICE_ID);
    $services->alias(UploadedFileFactoryInterface::class, HTTP_DISCOVERY_SERVICE_ID);
    $services->alias(UriFactoryInterface::class, HTTP_DISCOVERY_SERVICE_ID);
};
