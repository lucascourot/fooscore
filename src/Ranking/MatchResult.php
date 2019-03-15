<?php

declare(strict_types=1);

namespace Fooscore\Ranking;

final class MatchResult
{
    /**
     * @var WinningTeam
     */
    private $winningTeam;

    /**
     * @var LosingTeam
     */
    private $losingTeam;

    public function __construct(WinningTeam $winningTeam, LosingTeam $losingTeam)
    {
        $this->winningTeam = $winningTeam;
        $this->losingTeam = $losingTeam;
    }

    public function winningTeam(): WinningTeam
    {
        return $this->winningTeam;
    }

    public function losingTeam(): LosingTeam
    {
        return $this->losingTeam;
    }
}
