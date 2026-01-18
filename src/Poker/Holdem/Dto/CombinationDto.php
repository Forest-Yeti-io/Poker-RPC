<?php

namespace App\Poker\Holdem\Dto;

use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;

class CombinationDto
{
    /**
     * @var Card[]
     */
    private array $cards = [];

    public function getCards(): array
    {
        return $this->cards;
    }

    public function addCard(Card $card): self
    {
        $this->cards[] = $card;

        return $this;
    }
}
