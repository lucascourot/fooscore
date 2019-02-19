<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

interface DomainEvent
{
    public static function eventName(): string;

    public static function fromEventDataArray(array $eventData): DomainEvent;

    public function eventDataAsArray(): array;
}
