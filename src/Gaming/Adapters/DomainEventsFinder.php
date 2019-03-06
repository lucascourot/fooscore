<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Adapters;

interface DomainEventsFinder
{
    public function getDomainEventsClassesIndexedByNames(): array;
}
