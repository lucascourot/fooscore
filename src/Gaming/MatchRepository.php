<?php

declare(strict_types=1);

namespace Fooscore\Gaming;

interface MatchRepository
{
    public function save(Match $match): void;

    public function get(MatchId $matchId): Match;
}
