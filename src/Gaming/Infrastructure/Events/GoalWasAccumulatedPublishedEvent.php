<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Infrastructure\Events;

use Fooscore\Gaming\Match\GoalWasAccumulated;
use Fooscore\Gaming\Match\MatchId;

final class GoalWasAccumulatedPublishedEvent extends PublishedEvent
{
    /** @var MatchId */
    private $matchId;

    /** @var GoalWasAccumulated */
    private $domainEvent;

    public function __construct(MatchId $matchId, GoalWasAccumulated $domainEvent)
    {
        $this->domainEvent = $domainEvent;
        $this->matchId = $matchId;
    }

    public function matchId() : MatchId
    {
        return $this->matchId;
    }

    public function domainEvent() : GoalWasAccumulated
    {
        return $this->domainEvent;
    }
}
