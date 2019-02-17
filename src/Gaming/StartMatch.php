<?php

namespace Fooscore\Gaming;

interface StartMatch
{
    public function startMatch(TeamBlue $teamBlue, TeamRed $teamRed): Match;
}
