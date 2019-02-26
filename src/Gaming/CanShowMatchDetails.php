<?php

declare(strict_types=1);

namespace Fooscore\Gaming;

use Fooscore\Gaming\Match\MatchId;
use Fooscore\Gaming\MatchDetails\MatchDetails;

interface CanShowMatchDetails
{
    public function showMatchDetails(MatchId $matchId): MatchDetails;
}
