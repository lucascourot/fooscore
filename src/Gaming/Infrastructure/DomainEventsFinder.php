<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Infrastructure;

interface DomainEventsFinder
{
    public function getDomainEventsClassesIndexedByNames(): array;
}
