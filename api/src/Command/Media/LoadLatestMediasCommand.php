<?php

declare(strict_types=1);

namespace App\Command\Media;

use App\Messenger\Media\FetchLastMediasMessage;
use App\Messenger\Reddit\LoadRedditPostsMessage;
use App\Messenger\Twitter\LoadTweetsMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: 'app:media:load-latest')]
final class LoadLatestMediasCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->addOption('limit', 'l', InputOption::VALUE_REQUIRED, default: 100);
        $this->addOption('offset', 'o', InputOption::VALUE_REQUIRED, default: 0);
        $this->addOption('platform', 'p', InputOption::VALUE_REQUIRED, default: 'all');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $limit = (int) $input->getOption('limit');
        $offset = (int) $input->getOption('offset');
        $platform = (string) $input->getOption('platform');

        match ($platform) {
            'twitter' => $this->messageBus->dispatch(new LoadTweetsMessage($limit)),
            'reddit' => $this->messageBus->dispatch(new LoadRedditPostsMessage($limit)),
            'all' => $this->messageBus->dispatch(new FetchLastMediasMessage($limit, $offset)),
            default => throw new \InvalidArgumentException("Unsupported platform $platform"),
        };

        return 0;
    }
}
