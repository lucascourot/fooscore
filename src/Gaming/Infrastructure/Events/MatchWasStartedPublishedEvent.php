<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Infrastructure\Events;

use Fooscore\Gaming\Match\MatchId;
use Fooscore\Gaming\Match\MatchWasStarted;

final class MatchWasStartedPublishedEvent extends PublishedEvent
{
    /** @var MatchId */
    private $matchId;

    /** @var MatchWasStarted */
    private $domainEvent;

    public function __construct(MatchId $matchId, MatchWasStarted $domainEvent)
    {
        $this->domainEvent = $domainEvent;
        $this->matchId = $matchId;
    }

    public function matchId() : MatchId
    {
        return $this->matchId;
    }

    public function domainEvent() : MatchWasStarted
    {
        return $this->domainEvent;
    }
}
