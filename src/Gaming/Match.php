<?php

namespace Fooscore\Gaming;

final class Match
{
    /**
     * @var MatchId
     */
    private $matchId;

    public static function start(MatchId $matchId, TeamBlue $teamBlue, TeamRed $teamRed): self
    {
        $self = new self();
        $self->matchId = $matchId;

        return $self;
    }

    public function id(): MatchId
    {
        return $this->matchId;
    }
}
