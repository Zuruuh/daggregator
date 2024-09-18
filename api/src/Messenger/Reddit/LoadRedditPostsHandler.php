<?php

declare(strict_types=1);

namespace App\Messenger\Reddit;

use App\DTO\Media\SaveableMediaDTO;
use App\DTO\Reddit\RedditVideoDTO;
use App\DTO\Reddit\RedditVideosDTO;
use App\Model\MediaType;
use App\Model\Platform;
use App\Service\Media\MediaBatchPersister;
use App\Service\Reddit\RedditCookieJar;
use App\Service\Reddit\RedditHttpClient;
use App\Service\SrcsetResolver;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;

#[AsMessageHandler(handles: LoadRedditPostsMessage::class)]
final readonly class LoadRedditPostsHandler
{
    public function __construct(
        private RedditHttpClient $redditHttpClient,
        private RedditCookieJar $cookieJar,
        private SrcsetResolver $srcsetResolver,
        private LoggerInterface $logger,
        private MediaBatchPersister $mediaBatchPersister,
        private SerializerInterface $serializer,
        #[Autowire('%env(REDDIT_USERNAME)%')]
        private string $redditUsername,
    ) {
    }

    public function __invoke(LoadRedditPostsMessage $message): void
    {
        if ($this->cookieJar->cookie('reddit_session') === null) {
            throw new \Exception('no available reddit_session cookie!');
        }

        $limit = $message->limit;

        $url = "/user/{$this->redditUsername}/saved/";
        $posts = [];

        while ($limit > 0) {
            if (count($posts) > 24) {
                $this->mediaBatchPersister->persistMultiple($posts);
                $posts = [];
            }

            $response = $this->redditHttpClient->request('GET', $url);
            $content = $response->getContent();
            file_put_contents('var/reddit.last.html', $content);

            $crawler = new Crawler($content);

            $articles = $crawler->filter('shreddit-post');
            /**
             * @var list<SaveableMediaDTO>
             */
            $dtos = $articles->each(function (Crawler $node) {
                $redditType = $node->attr('post-type');
                $type = match ($redditType) {
                    'image' => MediaType::Image,
                    'gallery' => MediaType::Image,
                    'gif' => MediaType::Image,
                    'video' => MediaType::Video,
                    'crosspost' => null,
                    'link' => null,
                    'multi_media' => null,
                    default => throw new \Exception('Unsupported media type: ' . $node->attr('post-type')),
                };

                if ($type === null) {
                    return null;
                }

                $urls = match ($redditType) {
                    'image', 'gallery' => $this->gatherUrlsInGallery($node),
                    'gif' => [(string) $node->attr('content-href')],
                    'video' => (function () use ($node): array {
                        $rawVideos = $node->filter('shreddit-player')->first()->attr('packaged-media-json');
                        $videos = $this->serializer->deserialize($rawVideos, RedditVideosDTO::class, 'json')->videos;

                        usort($videos, static fn (RedditVideoDTO $a, RedditVideoDTO $b): int => $b->width <=> $a->width);

                        return [$videos[0]->url];
                    })(),
                    default => $this->logger->info("Unsupported reddit type: `$redditType`"),
                };

                assert(is_array($urls));
                $urls = array_filter($urls, is_string(...));

                if ($urls === []) {
                    return null;
                }

                return new SaveableMediaDTO(
                    id: $node->attr('author-id') . $node->attr('id'),
                    platform: Platform::Reddit,
                    author: $node->attr('author'),
                    title: (string) $node->attr('post-title'),
                    type: $type,
                    description: $node->attr('subreddit-prefixed-name'),
                    tags: $node
                        ->filter('shreddit-post-flair div.flair-content')
                        ->each(fn (Crawler $node) => $node->text()),
                    urls: $urls,
                );
            });

            $parsed = count($dtos);

            $dtos = array_filter($dtos, is_object(...));
            array_push($posts, ...$dtos);

            $next = $crawler->filter('faceplate-partial[method="GET"][src][loading="programmatic"]');
            if ($next->count() === 0) {
                $this->logger->error('Cannot go further ? Failing silently.');

                break;
            }

            $url = html_entity_decode((string) $next->attr('src'));

            $limit -= $parsed;
        }

        if (count($posts) > 0) {
            $this->mediaBatchPersister->persistMultiple($posts);
        }
    }

    /**
     * @return list<?string>
     */
    private function gatherUrlsInGallery(Crawler $node): array
    {
        return $node->filter('img[role="presentation"]')->each(function (Crawler $node): ?string {
            $srcset = $node->attr('srcset') ?? $node->attr('data-lazy-srcset');

            if ($srcset === '') {
                $src = $node->attr('src') ?? '';

                if ($src === '') {
                    $this->logger->warning('Could not find an src for an <img /> tag with role="presentation" and no srcset or data-lazy-srcset');

                    return null;
                }

                return $src;
            }

            if ($srcset === null) {
                $this->logger->warning('Could not find an srcset for an <img /> tag with role="presentation" at ');

                return null;
            }

            return $this->srcsetResolver->getBestQualityUrl($srcset);
        });
    }
}
