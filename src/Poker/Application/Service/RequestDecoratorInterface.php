<?php

namespace App\Poker\Application\Service;

use Symfony\Component\HttpFoundation\Request;

interface RequestDecoratorInterface
{
    public function init(Request $request): RequestDecoratorInterface;

    public function getMethod(): string;
    public function getParams(): array;
    public function validateToken(string $correctlyApplicationSecretToken): self;
}
