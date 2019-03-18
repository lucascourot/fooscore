<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

interface DomainEvent
{
    public static function eventName() : string;

    /**
     * @param mixed[] $eventData
     */
    public static function fromEventDataArray(array $eventData) : DomainEvent;

    /**
     * @return mixed[]
     */
    public function eventDataAsArray() : array;
}
