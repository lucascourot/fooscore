<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

trait BackAndFrontPlayers
{
    /**
     * @var Player
     */
    public $back;

    /**
     * @var Player
     */
    public $front;

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
