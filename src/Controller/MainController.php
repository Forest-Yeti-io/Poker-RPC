<?php

namespace App\Controller;

use App\Infrastructure\Application\Service\RequestDecorator;
use App\Poker\Application\Exception\ApplicationException;
use App\Poker\Application\Service\ApplicationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    public function __construct(
        private readonly ApplicationInterface $application,
        #[Autowire('%app.secret_token%')]
        private readonly string $secretApplicationToken
    ) {
    }

    #[Route(path: '/main', name: 'main', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $requestDecorator = (new RequestDecorator())
                ->init($request)
                ->validateToken($this->secretApplicationToken);

            return new JsonResponse(
                $this->application->call($requestDecorator->getMethod(), $requestDecorator->getParams())
            );
        } catch (ApplicationException $e) {
            return new JsonResponse(
                ['message' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
