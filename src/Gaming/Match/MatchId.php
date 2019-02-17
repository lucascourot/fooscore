<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

use Ramsey\Uuid\UuidInterface;

final class MatchId
{
    /**
     * @var UuidInterface
     */
    private $uuid;

    public function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    public function value(): UuidInterface
    {
        return $this->uuid;
    }
}
