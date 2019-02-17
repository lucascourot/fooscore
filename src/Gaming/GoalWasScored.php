<?php

declare(strict_types=1);

namespace Fooscore\Gaming;

final class GoalWasScored implements DomainEvent
{
    /**
     * @var Scorer
     */
    private $scorer;

    public function __construct(Scorer $scorer)
    {
        $this->scorer = $scorer;
    }

    public function getScorer(): Scorer
    {
        return $this->scorer;
    }
}
