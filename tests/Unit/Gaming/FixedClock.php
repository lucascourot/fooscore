<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Gaming;

use DateTimeImmutable;
use Fooscore\Gaming\Match\Clock;

final class FixedClock implements Clock
{
    /** @var DateTimeImmutable */
    private $fixedTime;

    public function __construct(DateTimeImmutable $fixedTime)
    {
        $this->fixedTime = $fixedTime;
    }

    public function now() : DateTimeImmutable
    {
        return $this->fixedTime;
    }

    public function tick(DateTimeImmutable $newFixedTime) : void
    {
        $this->fixedTime = $newFixedTime;
    }
}
