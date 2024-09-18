<?php

declare(strict_types=1);

use Symfony\Component\HttpFoundation\Response;
use Symfony\Config\FrameworkConfig;

const USER_AGENT_HEADER = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36';

return static function (FrameworkConfig $framework): void {
    $framework->httpClient()
        ->defaultOptions()
        ->httpVersion('2.0')
        ->header('user-agent', USER_AGENT_HEADER)
        ->retryFailed()
        ->enabled(true)
        ->maxRetries(5)
        ->delay(30 * 1000) // in ms
        ->multiplier(3)
        ->httpCode((string) Response::HTTP_TOO_MANY_REQUESTS, [])
    ;

    $framework
        ->httpClient()
        ->scopedClient('twitter_client')
        ->baseUri('%env(TWITTER_SCRAPPER_DSN)%')
    ;

    $framework
        ->httpClient()
        ->scopedClient('reddit_client')
        ->baseUri('https://www.reddit.com')
        ->httpVersion('1.1')
    ;

    $framework
        ->httpClient()
        ->scopedClient('meilisearch_client')
        ->baseUri('%env(MEILISEARCH_DSN)%')
    ;
};
