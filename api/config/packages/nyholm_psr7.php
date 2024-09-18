<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

/* use Http\Message\MessageFactory; */
/* use Http\Message\RequestFactory; */
/* use Http\Message\ResponseFactory; */
/* use Http\Message\StreamFactory; */
/* use Http\Message\UriFactory; */
use Nyholm\Psr7\Factory\HttplugFactory;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

const NYHOLM_PSR_17_FACTORY = 'nyholm.psr7.psr17_factory';
const NYHOLM_PSR_17_HTTPLUG_FACTORY = 'nyholm.psr7.httplug_factory';

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services(NYHOLM_PSR_17_FACTORY)->class(Psr17Factory::class);
    $services(NYHOLM_PSR_17_HTTPLUG_FACTORY)->class(HttplugFactory::class);

    // Register nyholm/psr7 services for autowiring with PSR-17 (HTTP factories)
    $services->alias(RequestFactoryInterface::class, NYHOLM_PSR_17_FACTORY);
    $services->alias(ResponseFactoryInterface::class, NYHOLM_PSR_17_FACTORY);
    $services->alias(ServerRequestFactoryInterface::class, NYHOLM_PSR_17_FACTORY);
    $services->alias(StreamFactoryInterface::class, NYHOLM_PSR_17_FACTORY);
    $services->alias(UploadedFileFactoryInterface::class, NYHOLM_PSR_17_FACTORY);
    $services->alias(UriFactoryInterface::class, NYHOLM_PSR_17_FACTORY);

    /* // Register nyholm/psr7 services for autowiring with HTTPlug factories */
    /* $services->alias(MessageFactory::class, NYHOLM_PSR_17_HTTPLUG_FACTORY); */
    /* $services->alias(RequestFactory::class, NYHOLM_PSR_17_HTTPLUG_FACTORY); */
    /* $services->alias(ResponseFactory::class, NYHOLM_PSR_17_HTTPLUG_FACTORY); */
    /* $services->alias(StreamFactory::class, NYHOLM_PSR_17_HTTPLUG_FACTORY); */
    /* $services->alias(UriFactory::class, NYHOLM_PSR_17_HTTPLUG_FACTORY); */
};
