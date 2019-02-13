<?php

namespace Fooscore\Gaming;

final class Gaming implements StartMatch
{
    /**
     * @var MatchIdGenerator
     */
    private $matchIdGenerator;

    public function __construct(MatchIdGenerator $matchIdGenerator)
    {
        $this->matchIdGenerator = $matchIdGenerator;
    }

    public function startMatch(TeamBlue $teamBlue, TeamRed $teamRed): MatchId
    {
        $match = Match::start($this->matchIdGenerator->generate(), $teamBlue, $teamRed);

        return $match->id();
    }
}
