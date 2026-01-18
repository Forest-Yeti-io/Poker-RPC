<?php

namespace App\Poker\Holdem\UseCase;

use App\Poker\Application\Exception\ApplicationException;
use App\Poker\Application\Service\UseCaseInterface;
use App\Poker\Common\Service\CardFactory;
use App\Poker\Common\Service\CombinationPresenter;
use App\Poker\Holdem\Dto\CombinationDto;
use App\Poker\Holdem\Dto\WinnerDto;
use ForestYeti\PokerKernel\CardDeck\Service\CardPresenter;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Dto\EquityResult;
use ForestYeti\PokerKernel\Evaluator\Dto\GameResult;
use ForestYeti\PokerKernel\Evaluator\Service\HoldemEquityCalculator;
use ForestYeti\PokerKernel\Evaluator\Service\HoldemEvaluator;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;

readonly class GetHoldemWinnerUseCase implements UseCaseInterface
{
    private const string METHOD = 'GetHoldemWinner';

    private HoldemEvaluator $holdemEvaluator;
    private CardPresenter $cardPresenter;
    private HoldemEquityCalculator $holdemEquityCalculator;

    public function __construct(
        private CardFactory $cardFactory,
        private CombinationPresenter $combinationPresenter
    ) {
        $this->holdemEvaluator = new HoldemEvaluator();
        $this->cardPresenter = new CardPresenter();
        $this->holdemEquityCalculator = new HoldemEquityCalculator();
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
        $winnerDto = $this->prepareParams($params);

        $gameResult = $this->holdemEvaluator->evaluate($winnerDto->getPlayers(), $winnerDto->getBoardCards());
        $equityResult = $this->holdemEquityCalculator->calculate($winnerDto->getBoardCards(), $winnerDto->getPlayers());

        return $this->buildOutput($gameResult, $equityResult);
    }

    /**
     * @throws ApplicationException
     */
    private function prepareParams(array $params): WinnerDto
    {
        $boardCards = $params['BoardCards'] ?? [];
        if (empty($boardCards) || !is_array($boardCards)) {
            throw new ApplicationException('Некорректно переданы данные о картах на столе');
        }

        $players = $params['Players'] ?? [];
        if (empty($players) || !is_array($players)) {
            throw new ApplicationException('Некорректно переданы данные о игроках');
        }

        $winnerDto = new WinnerDto();
        foreach ($boardCards as $boardCard) {
            $winnerDto->addBoardCard($this->cardFactory->factory($boardCard));
        }

        foreach ($players as $identifier => $holeCards) {
            $winnerDto->addPlayer(
                new Player($identifier, $this->cardFactory->factoryFromArray($holeCards))
            );
        }

        return $winnerDto;
    }

    private function buildOutput(GameResult $gameResult, EquityResult $equityResult): array
    {
        $output = [
            'resolvers' => [],
            'winners' => [],
        ];

        foreach ($gameResult->getResolverResults() as $resolverResult) {
            $output['resolvers'][] = [
                'playerIdentifier' => $resolverResult->getPlayer()->getIdentifier(),
                'score' => $resolverResult->getCombinationScore(),
                'combinationName' => $this->combinationPresenter->presetHoldem($resolverResult->getCombinationScore()),
                'playingCards' => array_map(fn (Card $card) => $this->cardPresenter->preset($card), $resolverResult->getPlayingCards()),
                'equity' => $equityResult->getEquity($resolverResult->getPlayer()),
            ];
        }

        foreach ($gameResult->getWinners() as $winner) {
            $output['winners'][] = $winner->getPlayer()->getIdentifier();
        }

        return $output;
    }
}
