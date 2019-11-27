<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Infrastructure\Events;

use Fooscore\Gaming\Match\MatchId;
use Fooscore\Gaming\Match\MiddlefieldGoalsWereValidatedByRegularGoal;

final class MiddlefieldGoalsWereValidatedByRegularGoalPublishedEvent extends PublishedEvent
{
    /** @var MatchId */
    private $matchId;

    /** @var MiddlefieldGoalsWereValidatedByRegularGoal */
    private $domainEvent;

    public function __construct(MatchId $matchId, MiddlefieldGoalsWereValidatedByRegularGoal $domainEvent)
    {
        $this->domainEvent = $domainEvent;
        $this->matchId = $matchId;
    }

    public function matchId() : MatchId
    {
        return $this->matchId;
    }

    public function domainEvent() : MiddlefieldGoalsWereValidatedByRegularGoal
    {
        return $this->domainEvent;
    }
}
