<?php

declare(strict_types=1);

namespace Fooscore\Ranking;

interface EloScoresRepository
{
    public function get(MatchResult $matchResult): EloScores;

    public function save(EloScores $updatedEloScores): void;
}
