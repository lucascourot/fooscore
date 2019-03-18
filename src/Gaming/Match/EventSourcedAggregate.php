<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

abstract class EventSourcedAggregate
{
    /** @var VersionedEvent[] */
    private $recordedEvents = [];

    /** @var int */
    private $aggregateVersion = 0;

    /**
     * @param VersionedEvent[] $versionedEvents
     *
     * @return static AggregateRoot
     */
    public static function reconstituteFromHistory(array $versionedEvents)
    {
        $aggregateRoot = new static();

        foreach ($versionedEvents as $versionedEvent) {
            $aggregateRoot->aggregateVersion = $versionedEvent->aggregateVersion();
            $aggregateRoot->apply($versionedEvent->domainEvent());
        }

        return $aggregateRoot;
    }

    /**
     * @return VersionedEvent[]
     */
    public function recordedEvents() : array
    {
        return $this->recordedEvents;
    }

    protected function recordThat(DomainEvent $event) : void
    {
        $this->aggregateVersion++;
        $this->recordedEvents[] = new VersionedEvent($this->aggregateVersion, $event);
        $this->apply($event);
    }

    abstract protected function apply(DomainEvent $event) : void;
}
