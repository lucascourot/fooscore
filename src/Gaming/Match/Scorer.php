<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

final class Scorer
{
    /**
     * @var string
     */
    private $team;

    /**
     * @var string
     */
    private $position;

    public static function fromTeamAndPosition(string $team, string $position): self
    {
        $team = strtolower($team);
        $position = strtolower($position);

        if (in_array($team, ['blue', 'red'], true) === false) {
            throw new \InvalidArgumentException('Unknown team.');
        }

        if (in_array($position, ['back', 'front'], true) === false) {
            throw new \InvalidArgumentException('Unknown position.');
        }

        $self = new self();
        $self->team = $team;
        $self->position = $position;

        return $self;
    }

    public function team(): string
    {
        return $this->team;
    }

    public function position(): string
    {
        return $this->position;
    }
}
