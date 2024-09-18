<?php

declare(strict_types=1);

use Symfony\Config\FlysystemConfig;

return static function (FlysystemConfig $flysystem): void {
    $flysystem
        ->storage('remote.medias')
        ->adapter('aws')
        /*
         * @phpstan-ignore-next-line
         */
        ->options([
            'client' => 'storage.aws',
            'bucket' => '%env(REMOTE_BUCKET_NAME)%',
        ]);

    $flysystem
        ->storage('local.medias')
        ->adapter('aws')
        /*
         * @phpstan-ignore-next-line
         */
        ->options([
            'client' => 'storage.local',
            'bucket' => '%env(LOCAL_BUCKET_NAME)%',
        ]);
};
