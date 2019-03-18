<?php

declare(strict_types=1);

namespace Fooscore\Ranking;

interface CanUpdateEloScore
{
    public function updatePlayersScores(MatchResult $matchResult) : EloScores;
}
