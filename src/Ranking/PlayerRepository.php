<?php

declare(strict_types=1);

namespace Fooscore\Ranking;

interface PlayerRepository
{
    public function get(string $id): Player;

    public function save(Player $player): bool;
}
