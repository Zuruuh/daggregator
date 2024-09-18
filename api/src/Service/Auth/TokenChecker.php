<?php

declare(strict_types=1);

namespace App\Service\Auth;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

final readonly class TokenChecker
{
    public function __construct(
        private RequestStack $requestStack,
        #[Autowire('%env(APP_TOKEN)%')]
        private string $token,
    ) {
    }

    public function check(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request === null) {
            throw new \Exception('No request found on top of request stack ?');
        }

        $route = (string) $request->get('_route');

        if (str_starts_with($route, 'app_')) {
            $token = $request->query->getString('token');

            if ($token !== $this->token) {
                throw new UnauthorizedHttpException('?token=', message: 'Invalid application token');
            }
        }
    }
}
