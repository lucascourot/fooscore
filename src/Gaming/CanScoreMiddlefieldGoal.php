<?php

declare(strict_types=1);

namespace Fooscore\Gaming;

use Fooscore\Gaming\Match\Match;
use Fooscore\Gaming\Match\MatchId;
use Fooscore\Gaming\Match\Scorer;

interface CanScoreMiddlefieldGoal
{
    public function scoreMiddlefieldGoal(MatchId $matchId, Scorer $scorer) : Match;
}
