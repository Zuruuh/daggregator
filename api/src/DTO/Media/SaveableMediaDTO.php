<?php

declare(strict_types=1);

namespace App\DTO\Media;

use App\Model\MediaType;
use App\Model\Platform;
use Symfony\Component\Clock\DatePoint;

final readonly class SaveableMediaDTO
{
    public string $id;
    public string $type;
    public string $savedAt;

    /**
     * @param list<string> $tags
     * @param list<string> $urls
     */
    public function __construct(
        string $id,
        Platform $platform,
        public string $title,
        public ?string $description,
        public ?string $author,
        public array $tags,
        MediaType $type,
        public array $urls,
    ) {
        $this->id = $platform->asId($id);
        $this->type = $type->toString();
        $this->savedAt = (new DatePoint())->format(\DateTimeInterface::RFC3339_EXTENDED);
    }
}
