<?php

declare(strict_types=1);

namespace App\Messenger\Reddit;

final readonly class LoadRedditPostsMessage
{
    public function __construct(public int $limit = 100)
    {
    }
}
