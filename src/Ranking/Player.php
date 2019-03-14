<?php

declare(strict_types=1);

namespace Fooscore\Ranking;

class Player
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var int
     */
    private $score;

    public function __construct(string $id, int $score)
    {
        $this->id = $id;
        $this->score = $score;
    }

    public function score(): int
    {
        return $this->score;
    }

    public function updateScore(int $gain): self
    {
        $this->score += $gain;

        return $this;
    }
}
