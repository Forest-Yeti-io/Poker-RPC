<?php

namespace App\Poker\Common\Service;

use App\Poker\Application\Exception\ApplicationException;
use ForestYeti\PokerKernel\CardDeck\Enum\CardRankEnum;
use ForestYeti\PokerKernel\CardDeck\Enum\CardSuitEnum;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;

class CardFactory
{
    /**
     * @throws ApplicationException
     */
    public function factory(string $rowCard): Card
    {
        $exploded = explode('-', $rowCard);
        if (count($exploded) !== 2) {
            throw new ApplicationException("Некорректная структура карты - $rowCard");
        }

        [$rank, $suit] = $exploded;

        $cardRankEnum = CardRankEnum::tryFrom($rank);
        $cardSuitEnum = CardSuitEnum::tryFrom($suit);
        if ($cardRankEnum === null || $cardSuitEnum === null) {
            throw new ApplicationException("Некорректная структура карты - $rowCard");
        }

        return new Card($cardRankEnum, $cardSuitEnum);
    }

    /**
     * @throws ApplicationException
     */
    public function factoryFromArray(array $rowCards): array
    {
        $result = [];
        foreach ($rowCards as $row) {
            $result[] = $this->factory($row);
        }

        return $result;
    }
}
