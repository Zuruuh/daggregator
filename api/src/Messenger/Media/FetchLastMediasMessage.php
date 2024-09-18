<?php

declare(strict_types=1);

namespace App\Messenger\Media;

final readonly class FetchLastMediasMessage
{
    public function __construct(
        public int $limit,
        public int $offset = 0
    ) {
    }
}
