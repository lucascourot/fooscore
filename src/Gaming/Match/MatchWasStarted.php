<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

use Ramsey\Uuid\Uuid;

final class MatchWasStarted implements DomainEvent
{
    /**
     * @var MatchId
     */
    private $matchId;

    /**
     * @var TeamBlue
     */
    private $teamBlue;

    /**
     * @var TeamRed
     */
    private $teamRed;

    public function __construct(MatchId $matchId, TeamBlue $teamBlue, TeamRed $teamRed)
    {
        $this->matchId = $matchId;
        $this->teamBlue = $teamBlue;
        $this->teamRed = $teamRed;
    }

    public function matchId(): MatchId
    {
        return $this->matchId;
    }

    public function teamBlue(): TeamBlue
    {
        return $this->teamBlue;
    }

    public function teamRed(): TeamRed
    {
        return $this->teamRed;
    }

    public static function eventName(): string
    {
        return 'match_was_started';
    }

    public static function fromEventDataArray(array $eventData): DomainEvent
    {
        return new self(
            new MatchId(Uuid::fromString($eventData['matchId'])),
            new TeamBlue($eventData['blue']['back'], $eventData['blue']['front']),
            new TeamRed($eventData['red']['back'], $eventData['red']['front'])
        );
    }

    public function eventDataAsArray(): array
    {
        return [
            'matchId' => $this->matchId->value()->toString(),
            'blue' => [
                'back' => $this->teamBlue->back(),
                'front' => $this->teamBlue->front(),
            ],
            'red' => [
                'back' => $this->teamRed->back(),
                'front' => $this->teamRed->front(),
            ],
        ];
    }
}
