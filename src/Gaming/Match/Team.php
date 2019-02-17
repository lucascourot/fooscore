<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

trait Team
{
    /**
     * @var string
     */
    private $back;

    /**
     * @var string
     */
    private $front;

    public function __construct(string $back, string $front)
    {
        $this->back = $back;
        $this->front = $front;
    }
}
