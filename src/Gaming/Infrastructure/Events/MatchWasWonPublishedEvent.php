<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Infrastructure\Events;

use Fooscore\Gaming\Match\MatchId;
use Fooscore\Gaming\Match\MatchWasWon;

final class MatchWasWonPublishedEvent extends PublishedEvent
{
    /** @var MatchId */
    private $matchId;

    /** @var MatchWasWon */
    private $domainEvent;

    public function __construct(MatchId $matchId, MatchWasWon $domainEvent)
    {
        $this->domainEvent = $domainEvent;
        $this->matchId = $matchId;
    }

    public function matchId() : MatchId
    {
        return $this->matchId;
    }

    public function domainEvent() : MatchWasWon
    {
        return $this->domainEvent;
    }
}
