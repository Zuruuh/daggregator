<?php

declare(strict_types=1);

namespace App\DTO\Reddit;

use Symfony\Component\Serializer\Attribute\SerializedPath;

final readonly class RedditVideosDTO
{
    /**
     * @param list<RedditVideoDTO> $videos
     */
    public function __construct(
        #[SerializedPath('[playbackMp4s][permutations]')]
        public array $videos
    ) {
    }
}
