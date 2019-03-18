<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

use DateTimeImmutable;

interface Clock
{
    public function now() : DateTimeImmutable;
}
