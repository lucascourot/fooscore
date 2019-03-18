<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Infrastructure;

use Fooscore\Gaming\Match\DomainEvent;
use Fooscore\Gaming\Match\MatchId;
use Symfony\Component\EventDispatcher\Event;

final class MatchSymfonyEvent extends Event
{
    /** @var MatchId */
    private $matchId;

    /** @var DomainEvent */
    private $domainEvent;

    public function __construct(MatchId $matchId, DomainEvent $domainEvent)
    {
        $this->domainEvent = $domainEvent;
        $this->matchId = $matchId;
    }

    public function matchId() : MatchId
    {
        return $this->matchId;
    }

    public function domainEvent() : DomainEvent
    {
        return $this->domainEvent;
    }
}
