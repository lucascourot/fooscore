<?php

namespace Fooscore\Gaming;

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
