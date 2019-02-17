<?php

declare(strict_types=1);

namespace Fooscore\Gaming;

final class MatchWasStarted implements DomainEvent
{
    /**
     * @var MatchId
     */
    private $matchId;

    /**
     * @var TeamBlue
     */
    private $teamBlue;

    /**
     * @var TeamRed
     */
    private $teamRed;

    public function __construct(MatchId $matchId, TeamBlue $teamBlue, TeamRed $teamRed)
    {
        $this->matchId = $matchId;
        $this->teamBlue = $teamBlue;
        $this->teamRed = $teamRed;
    }

    public function getMatchId(): MatchId
    {
        return $this->matchId;
    }
}
