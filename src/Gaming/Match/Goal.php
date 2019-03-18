<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

final class Goal
{
    /** @var int */
    private $number;

    /** @var Scorer */
    private $scorer;

    /** @var ScoredAt */
    private $scoredAt;

    public function __construct(int $number, Scorer $scorer, ScoredAt $scoredAt)
    {
        $this->number = $number;
        $this->scorer = $scorer;
        $this->scoredAt = $scoredAt;
    }

    public function number() : int
    {
        return $this->number;
    }

    public function scorer() : Scorer
    {
        return $this->scorer;
    }

    public function scoredAt() : ScoredAt
    {
        return $this->scoredAt;
    }
}
