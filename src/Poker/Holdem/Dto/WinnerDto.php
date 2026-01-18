<?php

namespace App\Poker\Holdem\Dto;

use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;

class WinnerDto
{
    /**
     * @var Card[]
     */
    private array $boardCards = [];

    /**
     * @var Player[]
     */
    private array $players = [];

    public function getBoardCards(): array
    {
        return $this->boardCards;
    }

    public function addBoardCard(Card $boardCards): self
    {
        $this->boardCards[] = $boardCards;

        return $this;
    }

    public function getPlayers(): array
    {
        return $this->players;
    }

    public function addPlayer(Player $players): self
    {
        $this->players[] = $players;

        return $this;
    }
}
