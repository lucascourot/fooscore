<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Adapters;

use Fooscore\Gaming\Match\DomainEvent;

final class SymfonyDomainEventsFinder implements DomainEventsFinder
{
    /**
     * @var array
     */
    private $domainEventsClassesIndexedByNames;

    public function __construct(array $domainEventClasses)
    {
        $this->domainEventsClassesIndexedByNames = [];

        /** @var DomainEvent $domainEventClass */
        foreach ($domainEventClasses as $domainEventClass) {
            $this->domainEventsClassesIndexedByNames[$domainEventClass::eventName()] = $domainEventClass;
        }
    }

    public function getDomainEventsClassesIndexedByNames(): array
    {
        return $this->domainEventsClassesIndexedByNames;
    }
}
