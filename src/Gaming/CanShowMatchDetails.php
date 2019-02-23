<?php

declare(strict_types=1);

namespace Fooscore\Gaming;

use Fooscore\Gaming\Match\Match;
use Fooscore\Gaming\Match\MatchId;

interface CanShowMatchDetails
{
    public function showMatchDetails(MatchId $matchId): Match;
}
