<?php

declare(strict_types=1);

namespace App\Service\Reddit;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpClient\DecoratorTrait;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class RedditHttpClient
{
    use DecoratorTrait;

    private const DEFAULT_HEADERS = [
        'authority' => 'www.reddit.com',
        'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'accept-language' => 'en-US,en;q=0.9',
        'cache-control' => 'max-age=0',
        'dnt' => 1,
        'sec-ch-ua' => '"Chromium";v="122", "Not(A:Brand";v="24", "Google Chrome";v="122"',
        'sec-ch-ua-mobile' => '?0',
        'sec-ch-ua-platform' => 'macOS',
        'sec-fetch-dest' => 'document',
        'sec-fetch-mode' => 'navigate',
        'sec-fetch-site' => 'none',
        'sec-fetch-user' => '?1',
        'upgrade-insecure-requests' => 1,
        'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        'referer' => 'https://www.reddit.com',
        'origin' => 'https://www.reddit.com',
    ];

    public function __construct(
        #[Autowire(service: 'reddit_client')] HttpClientInterface $redditHttpClient,
        private readonly RedditCookieJar $cookieJar,
    ) {
        $this->client = $redditHttpClient;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        if ($this->cookieJar->empty()) {
            $response = $this->client->request('GET', '/');
            $this->cookieJar->merge($response->getHeaders(throw: true)['set-cookie'] ?? []);
        }

        if (!array_key_exists('headers', $options)) {
            $options['headers'] = [];
        }

        $defaultCookies = $this->cookieJar->cookies();
        $defaultCookies = array_map(static fn (Cookie $c) => "{$c->getName()}={$c->getValue()}", $defaultCookies);
        $defaultCookies = implode(';', $defaultCookies);

        /* if (array_key_exists('cookie', $options['headers'])) { */
        /*     $cookies = $options['headers']['cookie']; */
        /*     $cookies = is_array($cookies) ? $cookies : [$cookies]; */

        /*     $options['headers']['cookie'] = [$cookies, $defaultCookies]; */
        /* } else { */
        $options['headers']['cookie'] = $defaultCookies;
        /* } */

        $options['headers'] = [...self::DEFAULT_HEADERS, ...$options['headers']];

        $response = $this->client->request($method, $url, $options);
        $this->cookieJar->merge($response->getHeaders(throw: false)['set-cookie'] ?? []);
        $response->getContent(false);

        return $response;
    }
}
