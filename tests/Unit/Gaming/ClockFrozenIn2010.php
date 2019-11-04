<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Gaming;

use DateInterval;
use DateTimeImmutable;
use Fooscore\Gaming\Match\Clock;

final class ClockFrozenIn2010 implements Clock
{
    /** @var DateTimeImmutable */
    private $fixedTime;

    public function __construct()
    {
        $this->fixedTime = new DateTimeImmutable('2010-01-01 00:00:00');
    }

    public function now() : DateTimeImmutable
    {
        return $this->fixedTime;
    }

    public function add1Min30Sec() : void
    {
        $this->fixedTime = $this->fixedTime->add(new DateInterval('PT1M30S'));
    }
}
