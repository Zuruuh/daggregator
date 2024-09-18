<?php

declare(strict_types=1);

namespace App\DTO\Reddit;

use Symfony\Component\Serializer\Attribute\SerializedPath;

final readonly class RedditVideoDTO
{
    public function __construct(
        #[SerializedPath('[source][url]')]
        public readonly string $url,
        #[SerializedPath('[source][dimensions][width]')]
        public readonly int $width,
        #[SerializedPath('[source][dimensions][height]')]
        public readonly int $height,
    ) {
    }
}
