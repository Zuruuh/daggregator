<?php

declare(strict_types=1);

namespace App\Controller\Media;

use App\Model\Media;
use App\Service\Auth\TokenChecker;
use Meilisearch\Client as Meilisearch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class SearchController extends AbstractController
{
    public function __construct(
        private Meilisearch $meilisearch,
        private TokenChecker $tokenChecker,
    ) {
    }

    #[Route('/medias/search', methods: Request::METHOD_GET)]
    public function __invoke(
        #[MapQueryParameter] string $query,
        #[MapQueryParameter] int $offset = 0,
        #[MapQueryParameter] int $limit = 50,
    ): Response {
        $this->tokenChecker->check();
        $query = trim($query);

        $index = $this->meilisearch->index(Media::INDEX_NAME);
        $sort = $query === '' ? ['sort' => ['saved_at:desc']] : [];

        $results = $index->search($query, ['limit' => $limit, 'offset' => $offset, ...$sort]);

        return $this->json($results->toArray());
    }
}
