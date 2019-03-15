<?php

declare(strict_types=1);

namespace Fooscore\Ranking;

interface CanUpdateScore
{
    public function updateScore(string $winningPlayerId1, string $winningPlayerId2, string $losingPlayerId1, string $losingPlayerId2): void;
}
