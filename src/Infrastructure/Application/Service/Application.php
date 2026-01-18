<?php

namespace App\Infrastructure\Application\Service;

use App\Poker\Application\Exception\ApplicationException;
use App\Poker\Application\Service\ApplicationInterface;
use App\Poker\Application\Service\UseCaseInterface;

class Application implements ApplicationInterface
{
    /**
     * @var UseCaseInterface[]
     */
    private array $useCases = [];

    /**
     * @param UseCaseInterface[] $useCases
     */
    public function __construct(iterable $useCases)
    {
        foreach ($useCases as $useCase) {
            $this->useCases[$useCase->getMethodName()] = $useCase;
        }
    }

    /**
     * @throws ApplicationException
     */
    public function call(string $method, array $params = []): array
    {
        $useCase = $this->useCases[$method] ?? null;
        if ($useCase === null) {
            throw new ApplicationException("Метод с названием - $method не зарегистрирован");
        }

        return $useCase->call($params);
    }
}
