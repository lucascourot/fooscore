<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Infrastructure\Events;

use Fooscore\Gaming\Match\MatchId;
use Fooscore\Gaming\Match\MiddlefieldGoalWasScored;

final class MiddlefieldGoalWasScoredPublishedEvent extends PublishedEvent
{
    /** @var MatchId */
    private $matchId;

    /** @var MiddlefieldGoalWasScored */
    private $domainEvent;

    public function __construct(MatchId $matchId, MiddlefieldGoalWasScored $domainEvent)
    {
        $this->domainEvent = $domainEvent;
        $this->matchId = $matchId;
    }

    public function matchId() : MatchId
    {
        return $this->matchId;
    }

    public function domainEvent() : MiddlefieldGoalWasScored
    {
        return $this->domainEvent;
    }
}
