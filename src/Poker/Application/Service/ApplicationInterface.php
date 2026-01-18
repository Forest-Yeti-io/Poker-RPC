<?php

namespace App\Poker\Application\Service;

interface ApplicationInterface
{
    public function call(string $method, array $params = []): array;
}
