<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

final class MiddlefieldGoalsWereValidatedByRegularGoal implements DomainEvent
{
    /** @var Goal */
    private $goal;

    /** @var int */
    private $numberOfGoalsToValidate;

    public function __construct(Goal $goal, int $numberOfGoalsToValidate)
    {
        $this->goal = $goal;
        $this->numberOfGoalsToValidate = $numberOfGoalsToValidate;
    }

    public function goal() : Goal
    {
        return $this->goal;
    }

    public function numberOfGoalsToValidate() : int
    {
        return $this->numberOfGoalsToValidate;
    }

    public static function eventName() : string
    {
        return 'middlefield_goals_were_validated_by_regular_goal';
    }

    /**
     * {@inheritdoc}
     */
    public static function fromEventDataArray(array $eventData) : DomainEvent
    {
        return new self(
            new Goal(
                $eventData['number'],
                Scorer::fromTeamAndPosition($eventData['team'], $eventData['position']),
                new ScoredAt($eventData['sec'])
            ),
            $eventData['numberOfGoalsToValidate']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function eventDataAsArray() : array
    {
        return [
            'number' => $this->goal->number(),
            'team' => $this->goal->scorer()->team(),
            'position' => $this->goal->scorer()->position(),
            'sec' => $this->goal->scoredAt()->sec(),
            'numberOfGoalsToValidate' => $this->numberOfGoalsToValidate,
        ];
    }
}
