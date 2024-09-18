<?php

declare(strict_types=1);

namespace App\Command\Migration;

use Meilisearch\Client as Meilisearch;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:migration:initiate-meilisearch-media-index')]
final class InitiateMeilisearchMediaIndex extends Command
{
    public function __construct(
        private Meilisearch $meilisearch,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->meilisearch->createIndex('medias');
        $this->meilisearch->index('medias')->updateSortableAttributes(['saved_at']);

        return 0;
    }
}
