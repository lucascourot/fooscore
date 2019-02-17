<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

interface MatchIdGenerator
{
    public function generate(): MatchId;
}
