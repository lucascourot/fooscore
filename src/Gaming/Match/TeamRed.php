<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

final class TeamRed
{
    /**
     * @var Player
     */
    private $back;

    /**
     * @var Player
     */
    private $front;

    public function __construct(Player $back, Player $front)
    {
        $this->back = $back;
        $this->front = $front;
    }

    public function back(): Player
    {
        return $this->back;
    }

    public function front(): Player
    {
        return $this->front;
    }
}
