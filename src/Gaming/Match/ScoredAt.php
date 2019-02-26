<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

use DateTimeImmutable;

final class ScoredAt
{
    /**
     * @var int
     */
    private $sec;

    public function __construct(int $sec)
    {
        $this->sec = $sec;
    }

    public static function fromDifference(DateTimeImmutable $startTime, DateTimeImmutable $scoredTime): self
    {
        return new self(
            $scoredTime->getTimestamp() - $startTime->getTimestamp()
        );
    }

    public function sec(): int
    {
        return $this->sec;
    }
}
