<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Infrastructure;

use Fooscore\Gaming\Match\DomainEvent;

final class SymfonyDomainEventsFinder implements DomainEventsFinder
{
    /** @var string[]|DomainEvent[] */
    private $domainEventsClassesIndexedByNames;

    /**
     * @param string[]|DomainEvent[] $domainEventClasses
     */
    public function __construct(array $domainEventClasses)
    {
        $this->domainEventsClassesIndexedByNames = [];

        /** @var DomainEvent|string $domainEventClass */
        foreach ($domainEventClasses as $domainEventClass) {
            $this->domainEventsClassesIndexedByNames[$domainEventClass::eventName()] = $domainEventClass;
        }
    }

    /**
     * @return string[]|DomainEvent[]
     */
    public function getDomainEventsClassesIndexedByNames() : array
    {
        return $this->domainEventsClassesIndexedByNames;
    }
}
