<?php

declare(strict_types=1);

namespace Fooscore\Gaming;

interface StartMatch
{
    public function startMatch(TeamBlue $teamBlue, TeamRed $teamRed): Match;
}
