<?php

declare(strict_types=1);

namespace App\Messenger\Twitter;

use App\DTO\Twitter\Tweet;
use App\Service\Media\MediaBatchPersister;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsMessageHandler(handles: LoadTweetsMessage::class)]
final readonly class LoadTweetsHandler
{
    private const GET_TWEETS_PATH = '/tweets';

    public function __construct(
        #[Autowire(service: 'twitter_client')] private HttpClientInterface $twitterHttpClient,
        private MediaBatchPersister $mediaBatchPersister,
        private SerializerInterface $serializer,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(LoadTweetsMessage $message): void
    {
        $response = $this->twitterHttpClient->request('GET', self::GET_TWEETS_PATH, [
            'query' => [
                'limit' => $message->limit,
            ],
            'timeout' => 10,
        ]);

        $buffer = tmpfile();
        assert(is_resource($buffer));

        foreach ($this->twitterHttpClient->stream($response, 360.0) as $chunk) {
            if ($chunk->isTimeout()) {
                $this->logger->critical('Twitter request timed out after 360 seconds!');

                break;
            } elseif ($chunk->getError() !== null) {
                $this->logger->critical('An error occured while streaming twitter content', ['error' => $chunk->getError()]);

                break;
            } elseif ($chunk->getContent() !== '') {
                $this->logger->notice('Writing temporary file next tweet');
                fwrite($buffer, $chunk->getContent());
            }
        }

        fseek($buffer, 0);

        $tweets = [];
        while (true) {
            $line = fgets($buffer);

            if ($line === false) {
                break;
            }

            if ($line === '') {
                continue;
            }

            if (count($tweets) === 24) {
                $this->mediaBatchPersister->persistMultiple($tweets);
                $tweets = [];
            }

            $tweet = $this->serializer->deserialize(
                $line,
                Tweet::class,
                'json'
            );

            assert($tweet instanceof Tweet);
            $tweet = $tweet->into();

            if ($tweet !== null) {
                $tweets[] = $tweet;
            }
        }

        if ($tweets !== []) {
            $this->mediaBatchPersister->persistMultiple($tweets);
        }
    }
}
