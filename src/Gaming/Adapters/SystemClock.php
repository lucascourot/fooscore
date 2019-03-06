<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Adapters;

use DateTimeImmutable;
use Fooscore\Gaming\Match\Clock;

final class SystemClock implements Clock
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
