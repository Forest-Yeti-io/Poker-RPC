<?php

namespace App\Poker\Application\Service;

interface UseCaseInterface
{
    public function getMethodName(): string;
    public function call(array $params = []): array;
}
