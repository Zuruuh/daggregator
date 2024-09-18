<?php

declare(strict_types=1);

namespace App\Service\Media;

use Symfony\Component\Clock\DatePoint;

final readonly class LastFetchedDateService
{
    private const STORE_KEY = 'media:last_fetch_timestamp';
    private const DATE_FORMAT = \DateTimeInterface::ATOM;

    public function __construct(
        private \Redis $store,
    ) {
    }

    public function getLastFetchDate(): ?DatePoint
    {
        $date = (string) $this->store->get(self::STORE_KEY);

        try {
            return DatePoint::createFromFormat(self::DATE_FORMAT, $date);
        } catch (\Throwable) {
            return null;
        }
    }

    public function setLastFetchDate(): DatePoint
    {
        $now = new DatePoint();
        $this->store->set(self::STORE_KEY, $now->format(self::DATE_FORMAT));

        return $now;
    }
}
