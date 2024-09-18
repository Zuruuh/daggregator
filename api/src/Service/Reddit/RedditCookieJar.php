<?php

declare(strict_types=1);

namespace App\Service\Reddit;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

/**
 * @final unfinalized for lazy proxy
 */
#[Autoconfigure(lazy: true)]
class RedditCookieJar
{
    private const PERSISTENCE_KEY = 'reddit:cookies';

    /**
     * @var Collection<string, Cookie>
     */
    private ?Collection $cookies = null;

    public function __construct(
        /**
         * @readonly
         */
        private \Redis $redis,
        #[Autowire(service: 'serializer.encoder.json')]
        private EncoderInterface&DecoderInterface $jsonEncoder,
    ) {
        $this->load();
    }

    /**
     * @param list<string> $cookies
     */
    public function merge(array $cookies): void
    {
        $this->cookies ??= new ArrayCollection([]);

        if ($cookies === []) {
            return;
        }

        foreach ($cookies as $cookie) {
            $cookie = Cookie::fromString($cookie);

            $this->cookies->set($cookie->getName(), $cookie);
        }

        $this->persist();
    }

    private function persist(): void
    {
        if ($this->cookies === null) {
            return;
        }

        $this->redis->set(
            self::PERSISTENCE_KEY,
            $this->jsonEncoder->encode(
                $this->cookies->map(fn (Cookie $c) => $c->__toString())->toArray(), 'json'
            )
        );
    }

    public function load(): void
    {
        $cached = $this->redis->get(self::PERSISTENCE_KEY);

        if ($cached === false) {
            $this->cookies = new ArrayCollection([]);

            return;
        }

        $cookies = $this->jsonEncoder->decode($cached, 'json');
        $cookies = array_map(Cookie::fromString(...), $cookies);

        $this->cookies = new ArrayCollection([]);

        foreach ($cookies as $cookie) {
            $this->cookies->set((string) $cookie->getName(), $cookie);
        }
    }

    public function clear(): void
    {
        $this->cookies?->clear();
    }

    public function empty(): bool
    {
        return $this->cookies === null || $this->cookies->isEmpty();
    }

    /**
     * @return list<Cookie>
     */
    public function cookies(): array
    {
        return $this->cookies?->getValues() ?? [];
    }

    public function cookie(string $name): ?Cookie
    {
        return $this->cookies?->findFirst(
            static fn (string $_, Cookie $cookie): bool => $cookie->getName() === $name
                && $cookie->getValue() !== null
                && $cookie->getValue() !== ''
        );
    }

    public function addTokenCookie(string $token): void
    {
        $cookie = new Cookie(
            'reddit_session',
            $token,
            (new DatePoint())
                ->modify(sprintf('+%s seconds', 60 * 60 * 24 * 30)) // 1 month ttl
        );

        $this->cookies ??= new ArrayCollection();

        $this->cookies->set(
            'reddit_session',
            $cookie,
        );

        $this->persist();
    }
}
