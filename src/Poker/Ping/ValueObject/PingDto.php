<?php

namespace App\Poker\Ping\ValueObject;

readonly class PingDto
{
    public function __construct(
        private bool $withSayHello
    ) {
    }

    public function isWithSayHello(): bool
    {
        return $this->withSayHello;
    }
}
