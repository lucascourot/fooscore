<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

use DateTimeImmutable;
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

    /**
     * @var DateTimeImmutable
     */
    private $startedAt;

    public function __construct(MatchId $matchId, TeamBlue $teamBlue, TeamRed $teamRed, DateTimeImmutable $startedAt)
    {
        $this->matchId = $matchId;
        $this->teamBlue = $teamBlue;
        $this->teamRed = $teamRed;
        $this->startedAt = $startedAt;
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

    public function startedAt(): DateTimeImmutable
    {
        return $this->startedAt;
    }

    public static function eventName(): string
    {
        return 'match_was_started';
    }

    public static function fromEventDataArray(array $eventData): DomainEvent
    {
        return new self(
            new MatchId(Uuid::fromString($eventData['matchId'])),
            new TeamBlue(
                new Player($eventData['blue']['back']['id'], $eventData['blue']['back']['name']),
                new Player($eventData['blue']['front']['id'], $eventData['blue']['front']['name'])
            ),
            new TeamRed(
                new Player($eventData['red']['back']['id'], $eventData['red']['back']['name']),
                new Player($eventData['red']['front']['id'], $eventData['red']['front']['name'])
            ),
            new DateTimeImmutable($eventData['startedAt'])
        );
    }

    public function eventDataAsArray(): array
    {
        return [
            'matchId' => $this->matchId->value()->toString(),
            'blue' => [
                'back' => [
                    'id' => $this->teamBlue->back()->id(),
                    'name' => $this->teamBlue->back()->name(),
                ],
                'front' => [
                    'id' => $this->teamBlue->front()->id(),
                    'name' => $this->teamBlue->front()->name(),
                ],
            ],
            'red' => [
                'back' => [
                    'id' => $this->teamRed->back()->id(),
                    'name' => $this->teamRed->back()->name(),
                ],
                'front' => [
                    'id' => $this->teamRed->front()->id(),
                    'name' => $this->teamRed->front()->name(),
                ],
            ],
            'startedAt' => $this->startedAt->format(DateTimeImmutable::W3C),
        ];
    }
}
