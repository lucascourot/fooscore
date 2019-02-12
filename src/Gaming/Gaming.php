<?php

namespace Fooscore\Gaming;

final class Gaming implements StartMatch
{
    public function startMatch(TeamBlue $teamBlue, TeamRed $teamRed): ?int
    {
        $match = Match::start($teamBlue, $teamRed);

        return 1;
    }
}
