<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Infrastructure;

use Fooscore\Gaming\Match\MatchId;
use Fooscore\Gaming\Match\MatchIdGenerator;
use Ramsey\Uuid\Uuid;

final class MatchIdGeneratorUuidv4 implements MatchIdGenerator
{
    public function generate() : MatchId
    {
        return new MatchId(Uuid::uuid4());
    }
}
