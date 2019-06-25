<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Infrastructure\Events;

use Fooscore\Gaming\Match\MatchId;
use Symfony\Contracts\EventDispatcher\Event;

abstract class PublishedEvent extends Event
{
    abstract public function matchId() : MatchId;
}
