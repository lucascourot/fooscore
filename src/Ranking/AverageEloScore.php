<?php

declare(strict_types=1);

namespace Fooscore\Ranking;

final class AverageEloScore
{
    public static function team(int $playerAScore, int $playerBScore): int
    {
        $average = ($playerAScore + $playerBScore) / 2;

        return (int) ceil($average);
    }
}
