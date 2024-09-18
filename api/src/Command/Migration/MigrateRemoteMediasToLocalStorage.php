<?php

declare(strict_types=1);

namespace App\Command\Migration;

use League\Flysystem\Filesystem;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(name: 'app:migration:migrate-remote-medias-to-local-storage')]
final class MigrateRemoteMediasToLocalStorage extends Command
{
    public function __construct(
        // #[Autowire(service: 'local.medias')] private readonly Filesystem $localMediasStorage,
        // #[Autowire(service: 'remote.medias')] private readonly Filesystem $remoteMediasStorage,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        return 0;
    }
}
