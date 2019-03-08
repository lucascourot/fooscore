<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

final class Player
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }
}
