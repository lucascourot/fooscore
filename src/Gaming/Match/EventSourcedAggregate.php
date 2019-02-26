<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

trait EventSourcedAggregate
{
    /**
     * @var VersionedEvent[]
     */
    private $recordedEvents = [];

    /**
     * @var int
     */
    private $aggregateVersion = 0;

    /**
     * @return VersionedEvent[]
     */
    public function recordedEvents(): array
    {
        return $this->recordedEvents;
    }

    private function recordThat(DomainEvent $event): void
    {
        $this->aggregateVersion++;
        $this->recordedEvents[] = new VersionedEvent($this->aggregateVersion, $event);
        $this->apply($event);
    }

    /**
     * @param VersionedEvent[] $versionedEvents
     */
    public static function reconstituteFromHistory(array $versionedEvents): self
    {
        $self = new self();

        foreach ($versionedEvents as $versionedEvent) {
            $self->aggregateVersion = $versionedEvent->aggregateVersion();
            $self->apply($versionedEvent->domainEvent());
        }

        return $self;
    }
}
