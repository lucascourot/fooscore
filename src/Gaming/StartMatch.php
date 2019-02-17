<?php

declare(strict_types=1);

namespace Fooscore\Gaming;

use Fooscore\Gaming\Match\Match;
use Fooscore\Gaming\Match\TeamBlue;
use Fooscore\Gaming\Match\TeamRed;

interface StartMatch
{
    public function startMatch(TeamBlue $teamBlue, TeamRed $teamRed): Match;
}
