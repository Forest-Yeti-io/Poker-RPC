<?php

namespace App\Poker\Holdem\UseCase;

use App\Poker\Application\Exception\ApplicationException;
use App\Poker\Application\Service\UseCaseInterface;
use App\Poker\Common\Service\CardFactory;
use App\Poker\Common\Service\CombinationPresenter;
use App\Poker\Holdem\Dto\CombinationDto;
use ForestYeti\PokerKernel\CardDeck\Service\CardPresenter;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Service\HoldemEvaluator;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;

readonly class GetHoldemCombinationUseCase implements UseCaseInterface
{
    private const string METHOD = 'GetHoldemCombination';

    private HoldemEvaluator $holdemEvaluator;
    private CardPresenter $cardPresenter;

    public function __construct(
        private CardFactory $cardFactory,
        private CombinationPresenter $combinationPresenter
    ) {
        $this->holdemEvaluator = new HoldemEvaluator();
        $this->cardPresenter = new CardPresenter();
    }

    public function getMethodName(): string
    {
        return self::METHOD;
    }

    /**
     * @throws ApplicationException
     */
    public function call(array $params = []): array
    {
        $combinationDto = $this->prepareParams($params);

        $gameResult = $this->holdemEvaluator->evaluate([new Player('P1')], $combinationDto->getCards());

        $score = $gameResult->getResolverResults()[0]->getCombinationScore();
        return [
            'score' => $score,
            'combinationName' => $this->combinationPresenter->presetHoldem($score),
            'playingCards' => array_map(
                fn (Card $card) => $this->cardPresenter->preset($card),
                $gameResult->getResolverResults()[0]->getPlayingCards()
            ),
        ];
    }

    /**
     * @throws ApplicationException
     */
    private function prepareParams(array $params): CombinationDto
    {
        $cards = $params['Cards'] ?? [];
        if (empty($cards) || !is_array($cards)) {
            throw new ApplicationException('Некорректно переданы данные о картах');
        }

        $combinationDto = new CombinationDto();
        foreach ($cards as $card) {
            $combinationDto->addCard(
                $this->cardFactory->factory($card)
            );
        }

        return $combinationDto;
    }
}
