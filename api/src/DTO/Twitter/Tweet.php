<?php

declare(strict_types=1);

namespace App\DTO\Twitter;

use App\DTO\Media\SaveableMediaDTO;
use App\Model\Media;
use App\Model\MediaType;
use App\Model\Platform;
use Symfony\Component\Serializer\Attribute\SerializedPath;

final readonly class Tweet implements Media
{
    /**
     * @var list<string>
     */
    private array $tags;

    /**
     * @param list<string> $photos
     * @param list<string> $videos
     */
    public function __construct(
        #[SerializedPath('[id]')]
        public int $numericId,
        public string $author,
        public string $title,
        public array $photos,
        public array $videos,
    ) {
        preg_match_all('/#[a-zA-Z0-9_]+/m', $title, $matches);
        [$matches] = $matches;

        $this->tags = array_map(static fn (string $s) => trim($s, '#'), $matches);
    }

    public function getId(): string
    {
        return (string) $this->numericId;
    }

    public function getPlatform(): Platform
    {
        return Platform::Twitter;
    }

    public function getType(): ?MediaType
    {
        if ($this->photos !== []) {
            return MediaType::Image;
        } elseif ($this->videos !== []) {
            return MediaType::Video;
        }

        return null;
    }

    public function getUrls(): array
    {
        return match ($this->getType()) {
            MediaType::Image => $this->photos,
            MediaType::Video => $this->videos,
            default => [],
        };
    }

    public function into(): ?SaveableMediaDTO
    {
        $urls = $this->getUrls();
        $mediaType = $this->getType();

        if ($mediaType === null || $urls === []) {
            return null;
        }

        return new SaveableMediaDTO(
            (string) $this->numericId,
            Platform::Twitter,
            $this->title,
            null,
            $this->author,
            $this->tags,
            $mediaType,
            $urls,
        );
    }
}
