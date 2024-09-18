<?php

declare(strict_types=1);

namespace App\Messenger\Media;

use App\Messenger\Reddit\LoadRedditPostsMessage;
use App\Messenger\Twitter\LoadTweetsMessage;
use App\Service\Media\LastFetchedDateService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(handles: FetchLastMediasMessage::class)]
final readonly class FetchLastMediasHandler
{
    public function __construct(
        private LastFetchedDateService $lastFetchedDate,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(FetchLastMediasMessage $message): void
    {
        $this->lastFetchedDate->setLastFetchDate();

        $this->messageBus->dispatch(new LoadTweetsMessage($message->limit));
        $this->messageBus->dispatch(new LoadRedditPostsMessage($message->limit));
    }
}
