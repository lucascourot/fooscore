<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

final class MatchWasWon implements DomainEvent
{
    /** @var string */
    private $teamWinner;

    public function __construct(string $teamWinner)
    {
        $this->teamWinner = $teamWinner;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromEventDataArray(array $eventData) : DomainEvent
    {
        return new self(
            $eventData['teamWinner']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function eventDataAsArray() : array
    {
        return [
            'teamWinner' => $this->teamWinner,
        ];
    }

    public static function eventName() : string
    {
        return 'match_was_won';
    }

    public function teamWinner() : string
    {
        return $this->teamWinner;
    }
}
