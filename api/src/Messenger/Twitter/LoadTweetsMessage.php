<?php

declare(strict_types=1);

namespace App\Messenger\Twitter;

final readonly class LoadTweetsMessage
{
    public function __construct(
        public int $limit
    ) {
    }
}
