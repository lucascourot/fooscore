<?php

declare(strict_types=1);

namespace Fooscore\Gaming\MatchDetails;

use Fooscore\Gaming\Match\Goal;
use Fooscore\Gaming\Match\Match;
use RuntimeException;

/**
 * Match read model
 */
final class MatchDetails
{
    private const MINUTE_IN_SECONDS = 60;

    /**
     * @var Match
     */
    private $match;

    public static function fromMatch(Match $match): self
    {
        $self = new self();
        $self->match = $match;

        return $self;
    }

    public function lastScoredGoal(): Goal
    {
        if (count($this->match->scoredGoals()) === 0) {
            throw new RuntimeException('No goal scored yet.');
        }

        return array_values(
            array_slice($this->match->scoredGoals(), -1)
        )[0];
    }

    public function details(): array
    {
        return [
            'id' => $this->match->id()->value()->toString(),
            'isWon' => $this->match->isWon(),
            'score' => [
                'blue' => array_reduce($this->match->scoredGoals(), function (int $acc, Goal $goal): int {
                    return $goal->scorer()->team() === 'blue' ? ++$acc : $acc;
                }, 0),
                'red' => array_reduce($this->match->scoredGoals(), function (int $acc, Goal $goal): int {
                    return $goal->scorer()->team() === 'red' ? ++$acc : $acc;
                }, 0),
            ],
            'goals' => array_map(function (Goal $goal): array {
                return [
                    'id' => $goal->number(),
                    'scoredAt' => [
                        'min' => floor($goal->scoredAt()->sec() / self::MINUTE_IN_SECONDS),
                        'sec' => $goal->scoredAt()->sec() % self::MINUTE_IN_SECONDS,
                    ],
                    'scorer' => [
                        'team' => $goal->scorer()->team(),
                        'position' => $goal->scorer()->position(),
                    ],
                ];
            }, $this->match->scoredGoals()),
            'players' => [
                'blue' => [
                    'back' => [
                        'id' => $this->match->teamBlue()->back(),
                        'name' => $this->match->teamBlue()->back(),
                    ],
                    'front' => [
                        'id' => $this->match->teamBlue()->front(),
                        'name' => $this->match->teamBlue()->front(),
                    ],
                ],
                'red' => [
                    'back' => [
                        'id' => $this->match->teamRed()->back(),
                        'name' => $this->match->teamRed()->back(),
                    ],
                    'front' => [
                        'id' => $this->match->teamRed()->front(),
                        'name' => $this->match->teamRed()->front(),
                    ],
                ],
            ],
        ];
    }
}
