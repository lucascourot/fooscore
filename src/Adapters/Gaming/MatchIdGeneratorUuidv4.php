<?php

declare(strict_types=1);

namespace Fooscore\Adapters\Gaming;

use Fooscore\Gaming\MatchId;
use Fooscore\Gaming\MatchIdGenerator;
use Ramsey\Uuid\Uuid;

final class MatchIdGeneratorUuidv4 implements MatchIdGenerator
{
    public function generate(): MatchId
    {
        return new MatchId(Uuid::uuid4());
    }
}
