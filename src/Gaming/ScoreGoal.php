<?php

declare(strict_types=1);

namespace Fooscore\Gaming;

interface ScoreGoal
{
    public function scoreGoal(MatchId $matchId, Scorer $scorer): Match;
}
