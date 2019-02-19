<?php

declare(strict_types=1);

namespace Fooscore\Adapters\Gaming;

interface DomainEventsFinder
{
    public function getDomainEventsClassesIndexedByNames(): array;
}
