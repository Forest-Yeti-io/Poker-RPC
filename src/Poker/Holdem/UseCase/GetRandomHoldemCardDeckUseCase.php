<?php

namespace App\Poker\Holdem\UseCase;

use App\Poker\Application\Service\UseCaseInterface;
use ForestYeti\PokerKernel\CardDeck\Service\CardPresenter;
use ForestYeti\PokerKernel\CardDeck\Service\Factory\HoldemCardDeckFactory;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Random\Service\SimpleRandomCardShuffler;

readonly class GetRandomHoldemCardDeckUseCase implements UseCaseInterface
{
    private const string METHOD = 'GetHoldemRandomCardDeck';

    private HoldemCardDeckFactory $holdemCardDeckFactory;
    private SimpleRandomCardShuffler $simpleRandomCardShuffler;
    private CardPresenter $cardPresenter;

    public function __construct()
    {
        $this->holdemCardDeckFactory = new HoldemCardDeckFactory();
        $this->simpleRandomCardShuffler = new SimpleRandomCardShuffler();
        $this->cardPresenter = new CardPresenter();
    }

    public function getMethodName(): string
    {
        return self::METHOD;
    }

    public function call(array $params = []): array
    {
        $cardDeck = $this->holdemCardDeckFactory->factory();
        $cardDeck = $this->simpleRandomCardShuffler->shuffle($cardDeck);

        return [
            'cardDeck' => array_map(fn (Card $card) => $this->cardPresenter->preset($card), $cardDeck->toArray()),
        ];
    }
}
