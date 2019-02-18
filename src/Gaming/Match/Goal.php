<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

final class Goal
{
    /**
     * @var int
     */
    private $number;

    /**
     * @var Scorer
     */
    private $scorer;

    public function __construct(int $number, Scorer $scorer)
    {
        $this->number = $number;
        $this->scorer = $scorer;
    }

    public function number(): int
    {
        return $this->number;
    }
}
