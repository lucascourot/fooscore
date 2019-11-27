<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Infrastructure\Events;

use Fooscore\Gaming\Match\DomainEvent;
use Fooscore\Gaming\Match\GoalWasScored;
use Fooscore\Gaming\Match\MatchId;
use Fooscore\Gaming\Match\MatchWasStarted;
use Fooscore\Gaming\Match\MatchWasWon;
use Fooscore\Gaming\Match\MiddlefieldGoalsWereValidatedByRegularGoal;
use Fooscore\Gaming\Match\MiddlefieldGoalWasScored;

final class PublishedEventFactory
{
    public static function create(MatchId $matchId, DomainEvent $domainEvent) : PublishedEvent
    {
        if ($domainEvent instanceof GoalWasScored) {
            return new GoalWasScoredPublishedEvent($matchId, $domainEvent);
        }

        if ($domainEvent instanceof MiddlefieldGoalWasScored) {
            return new MiddlefieldGoalWasScoredPublishedEvent($matchId, $domainEvent);
        }

        if ($domainEvent instanceof MiddlefieldGoalsWereValidatedByRegularGoal) {
            return new MiddlefieldGoalsWereValidatedByRegularGoalPublishedEvent($matchId, $domainEvent);
        }

        if ($domainEvent instanceof MatchWasStarted) {
            return new MatchWasStartedPublishedEvent($matchId, $domainEvent);
        }

        /** @var MatchWasWon $matchWasWon */
        $matchWasWon = $domainEvent;

        return new MatchWasWonPublishedEvent($matchId, $matchWasWon);
    }
}
