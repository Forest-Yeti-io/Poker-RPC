<?php

namespace App\Poker\Ping\UseCase;

use App\Poker\Application\Exception\ApplicationException;
use App\Poker\Application\Service\UseCaseInterface;
use App\Poker\Ping\ValueObject\PingDto;

class PingUseCase implements UseCaseInterface
{
    private const string METHOD = 'Ping';

    public function getMethodName(): string
    {
        return self::METHOD;
    }

    /**
     * @throws ApplicationException
     */
    public function call(array $params = []): array
    {
        $pingDto = $this->prepareParams($params);

        return [
            'message' => $pingDto->isWithSayHello() ? 'Hello, World!' : 'Hello, This is Poker-RPC',
        ];
    }

    /**
     * @throws ApplicationException
     */
    private function prepareParams(array $params): PingDto
    {
        $withSayHello = $params['WithSayHello'] ?? null;
        if ($withSayHello !== null && !is_bool($withSayHello)) {
            throw new ApplicationException('Некорректный тип параметра - withSayHello, ожидается bool');
        }

        return new PingDto($withSayHello ?? false);
    }
}
