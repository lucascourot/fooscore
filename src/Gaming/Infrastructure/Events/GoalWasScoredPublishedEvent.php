<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Infrastructure\Events;

use Fooscore\Gaming\Match\GoalWasScored;
use Fooscore\Gaming\Match\MatchId;

final class GoalWasScoredPublishedEvent extends PublishedEvent
{
    /** @var MatchId */
    private $matchId;

    /** @var GoalWasScored */
    private $domainEvent;

    public function __construct(MatchId $matchId, GoalWasScored $domainEvent)
    {
        $this->domainEvent = $domainEvent;
        $this->matchId = $matchId;
    }

    public function matchId() : MatchId
    {
        return $this->matchId;
    }

    public function domainEvent() : GoalWasScored
    {
        return $this->domainEvent;
    }
}
