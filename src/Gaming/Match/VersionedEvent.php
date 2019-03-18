<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

final class VersionedEvent
{
    /** @var int */
    private $aggregateVersion;

    /** @var DomainEvent */
    private $domainEvent;

    public function __construct(int $aggregateVersion, DomainEvent $domainEvent)
    {
        $this->aggregateVersion = $aggregateVersion;
        $this->domainEvent = $domainEvent;
    }

    public function aggregateVersion() : int
    {
        return $this->aggregateVersion;
    }

    public function domainEvent() : DomainEvent
    {
        return $this->domainEvent;
    }
}
