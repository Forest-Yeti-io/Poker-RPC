<?php

namespace App\Poker\Common\Service;

use ForestYeti\PokerKernel\Evaluator\Enum\HoldemCombinationScoreEnum;

class CombinationPresenter
{
    public function presetHoldem(int $baseScore): string
    {
        return match ($baseScore) {
            HoldemCombinationScoreEnum::HighCard->value => 'High Card',
            HoldemCombinationScoreEnum::Pair->value => 'Pair',
            HoldemCombinationScoreEnum::TwoPair->value => 'Two Pair',
            HoldemCombinationScoreEnum::ThreeOfKind->value => 'Three Of Kind',
            HoldemCombinationScoreEnum::Straight->value => 'Straight',
            HoldemCombinationScoreEnum::Flash->value => 'Flash',
            HoldemCombinationScoreEnum::FullHouse->value => 'Full House',
            HoldemCombinationScoreEnum::FourOfKind->value => 'Four of Kind',
            HoldemCombinationScoreEnum::StraightFlash->value => 'Straight Flash',
            HoldemCombinationScoreEnum::RoyalFlash->value => 'Royal Flash',
            default => 'Unknown Combination',
        };
    }
}
