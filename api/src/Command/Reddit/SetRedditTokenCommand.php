<?php

declare(strict_types=1);

namespace App\Command\Reddit;

use App\Service\Reddit\RedditCookieJar;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:reddit:set-token')]
final class SetRedditTokenCommand extends Command
{
    public function __construct(
        private readonly RedditCookieJar $redditTokenHandler,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->addArgument('token', InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $token = (string) $input->getArgument('token');

        $this->redditTokenHandler->addTokenCookie($token);

        return 0;
    }
}
