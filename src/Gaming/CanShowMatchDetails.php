<?php

declare(strict_types=1);

namespace Fooscore\Gaming;

use Fooscore\Gaming\Match\MatchId;

interface CanShowMatchDetails
{
    /**
     * @return mixed[]
     */
    public function showMatchDetails(MatchId $matchId) : array;
}
