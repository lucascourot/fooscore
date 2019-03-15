<?php

declare(strict_types=1);

namespace Fooscore\Ranking;

trait Double
{
    /**
     * @var string
     */
    private $playerAId;

    /**
     * @var string
     */
    private $playerBId;

    public function __construct(string $playerAId, string $playerBId)
    {
        $this->playerAId = $playerAId;
        $this->playerBId = $playerBId;
    }

    public function playerAId(): string
    {
        return $this->playerAId;
    }

    public function playerBId(): string
    {
        return $this->playerBId;
    }
}
