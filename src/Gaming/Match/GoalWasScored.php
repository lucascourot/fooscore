<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

final class GoalWasScored implements DomainEvent
{
    /** @var Goal */
    private $goal;

    public function __construct(Goal $goal)
    {
        $this->goal = $goal;
    }

    public function goal() : Goal
    {
        return $this->goal;
    }

    public static function eventName() : string
    {
        return 'goal_was_scored';
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
            )
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
        ];
    }
}
