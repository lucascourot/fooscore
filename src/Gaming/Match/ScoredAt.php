<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

use DateTimeImmutable;

final class ScoredAt
{
    /**
     * @var int
     */
    private $min;

    /**
     * @var int
     */
    private $sec;

    public function __construct(int $min, int $sec)
    {
        $this->min = $min;
        $this->sec = $sec;
    }

    public static function fromDifference(DateTimeImmutable $startTime, DateTimeImmutable $scoredTime): self
    {
        return new self(
            $startTime->diff($scoredTime)->i,
            $startTime->diff($scoredTime)->s
        );
    }

    public function min(): int
    {
        return $this->min;
    }

    public function sec(): int
    {
        return $this->sec;
    }
}
