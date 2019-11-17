<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Infrastructure\Events;

use Fooscore\Gaming\Match\DomainEvent;
use Fooscore\Gaming\Match\GoalWasAccumulated;
use Fooscore\Gaming\Match\GoalWasScored;
use Fooscore\Gaming\Match\MatchId;
use Fooscore\Gaming\Match\MatchWasStarted;
use Fooscore\Gaming\Match\MatchWasWon;

final class PublishedEventFactory
{
    public static function create(MatchId $matchId, DomainEvent $domainEvent) : PublishedEvent
    {
        if ($domainEvent instanceof GoalWasScored) {
            return new GoalWasScoredPublishedEvent($matchId, $domainEvent);
        }

        if ($domainEvent instanceof GoalWasAccumulated) {
            return new GoalWasAccumulatedPublishedEvent($matchId, $domainEvent);
        }

        if ($domainEvent instanceof MatchWasStarted) {
            return new MatchWasStartedPublishedEvent($matchId, $domainEvent);
        }

        /** @var MatchWasWon $matchWasWon */
        $matchWasWon = $domainEvent;

        return new MatchWasWonPublishedEvent($matchId, $matchWasWon);
    }
}
