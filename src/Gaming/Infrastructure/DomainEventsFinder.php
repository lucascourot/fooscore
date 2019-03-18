<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Infrastructure;

use Fooscore\Gaming\Match\DomainEvent;

interface DomainEventsFinder
{
    /**
     * @return string[]|DomainEvent[]
     */
    public function getDomainEventsClassesIndexedByNames() : array;
}
