<?php

namespace Fooscore\Gaming;

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

    public function sameValueAs(MatchId $other): bool
    {
        return $this->uuid->equals($other->uuid);
    }
}
