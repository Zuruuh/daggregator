<?php

declare(strict_types=1);

namespace App\Service\Media;

use App\DTO\Media\SaveableMediaDTO;
use App\Model\Media;
use Meilisearch\Client as Meilisearch;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final readonly class MediaBatchPersister
{
    public function __construct(
        private Meilisearch $meilisearch,
        private NormalizerInterface $normalizer,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @param non-empty-list<SaveableMediaDTO>|SaveableMediaDTO $medias
     */
    public function persistMultiple(array|SaveableMediaDTO $medias): void
    {
        $medias = is_array($medias) ? $medias : [$medias];
        $this->logger->notice('Persisting {count} medias', [
            'count' => count($medias),
            'ids' => array_map(static fn (SaveableMediaDTO $media) => $media->id, $medias),
        ]);

        $index = $this->meilisearch->index(Media::INDEX_NAME);
        $documents = $this->normalizer->normalize($medias);
        assert(is_array($documents));

        $index->addDocuments($documents);
    }
}
