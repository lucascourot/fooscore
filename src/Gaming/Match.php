<?php

namespace Fooscore\Gaming;

final class Match
{
    public static function start(TeamBlue $teamBlue, TeamRed $teamRed): self
    {
        return new self();
    }
}
