<?php

namespace Fooscore\Gaming;

interface ScoreGoal
{
    public function scoreGoal(MatchId $matchId, Scorer $scorer): Match;
}
