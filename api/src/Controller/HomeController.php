<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final readonly class HomeController
{
    #[Route('/')]
    public function __invoke(): Response
    {
        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
