<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

final class GoalWasScored implements DomainEvent
{
    /**
     * @var Goal
     */
    private $goal;

    public function __construct(Goal $goal)
    {
        $this->goal = $goal;
    }

    public function goal(): Goal
    {
        return $this->goal;
    }
}
