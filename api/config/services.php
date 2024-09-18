<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Aws\S3\S3Client;
use League\Flysystem\Filesystem;
use Meilisearch\Client as Meilisearch;
use Symfony\Component\HttpClient\Psr18Client;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $root = dirname(__DIR__);

    $services->defaults()->autowire(true)->autoconfigure(true);
    $container->parameters()->set('.container.dumper.inline_factories', true);

    $services->load('App\\', "$root/src")->exclude("$root/src/Kernel.php");

    /* $services->defaults()->bind(Filesystem::class . ' $localMedias', service('local.medias')); */
    /* $services->defaults()->bind(Filesystem::class . ' $remoteMedias', service('remote.medias')); */

    // $services('storage.local', S3Client::class)->args([[
    //     'version' => 'latest',
    //     'region' => 'eu-west-3',
    //     'use_path_style_endpoint' => true,
    //     'endpoint' => env('LOCAL_BUCKET_HOST'),
    //     'credentials' => [
    //         'key' => env('LOCAL_BUCKET_ACCESS_KEY'),
    //         'secret' => env('LOCAL_BUCKET_SECRET_KEY'),
    //     ],
    // ]]);

    $services('storage.aws', S3Client::class)->args([[
        'version' => 'latest',
        'region' => 'eu-west-3',
        'use_path_style_endpoint' => true,
        'endpoint' => env('REMOTE_BUCKET_HOST'),
        'credentials' => [
            'key' => env('REMOTE_BUCKET_ACCESS_KEY'),
            'secret' => env('REMOTE_BUCKET_SECRET_KEY'),
        ],
    ]]);

    $services('meilisearch_psr_client', Psr18Client::class)->args([
        '$client' => service('meilisearch_client'),
    ]);

    $services(Meilisearch::class)->args([
        '$url' => env('MEILISEARCH_DSN'),
        '$apiKey' => env('MEILISEARCH_MASTER_KEY'),
        '$httpClient' => service('meilisearch_psr_client'),
    ]);

    $services->alias(\Redis::class, 'snc_redis.default');
};
