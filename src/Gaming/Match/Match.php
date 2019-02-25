<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

/**
 * Match aggregate root
 */
final class Match
{
    /**
     * @var MatchId
     */
    private $id;

    /**
     * @var Goal[]
     */
    private $scoredGoals = [];

    /**
     * @var VersionedEvent[]
     */
    private $recordedEvents = [];

    /**
     * @var int
     */
    private $aggregateVersion = 0;

    /**
     * @var TeamBlue
     */
    private $teamBlue;

    /**
     * @var TeamRed
     */
    private $teamRed;

    /**
     * @var \DateTimeImmutable
     */
    private $startedAt;

    public static function start(MatchId $matchId, TeamBlue $teamBlue, TeamRed $teamRed, Clock $clock): self
    {
        $self = new self();
        $self->recordThat(new MatchWasStarted($matchId, $teamBlue, $teamRed, $clock->now()));

        return $self;
    }

    public function scoreGoal(Scorer $scorer, Clock $clock): self
    {
        $this->recordThat(new GoalWasScored(
            new Goal(count($this->scoredGoals) + 1, $scorer, ScoredAt::fromDifference($this->startedAt, $clock->now()))
        ));

        return $this;
    }

    public function id(): MatchId
    {
        return $this->id;
    }

    public function lastScoredGoal(): Goal
    {
        if (count($this->scoredGoals) === 0) {
            throw new \RuntimeException('No goal scored yet.');
        }

        return array_values(
            array_slice($this->scoredGoals, -1)
        )[0];
    }

    /**
     * @return VersionedEvent[]
     */
    public function recordedEvents(): array
    {
        return $this->recordedEvents;
    }

    private function recordThat(DomainEvent $event): void
    {
        $this->aggregateVersion++;
        $this->recordedEvents[] = new VersionedEvent($this->aggregateVersion, $event);
        $this->apply($event);
    }

    private function apply(DomainEvent $event): void
    {
        if ($event instanceof MatchWasStarted) {
            $this->id = $event->matchId();
            $this->teamBlue = $event->teamBlue();
            $this->teamRed = $event->teamRed();
            $this->startedAt = $event->startedAt();

            return;
        }

        if ($event instanceof GoalWasScored) {
            $this->scoredGoals[] = $event->goal();

            return;
        }

        throw new \InvalidArgumentException(
            sprintf('Unknown domain event "%s"', get_class($event))
        );
    }

    /**
     * @param VersionedEvent[] $versionedEvents
     */
    public static function reconstituteFromHistory(array $versionedEvents): self
    {
        $self = new self();

        foreach ($versionedEvents as $versionedEvent) {
            $self->aggregateVersion = $versionedEvent->aggregateVersion();
            $self->apply($versionedEvent->domainEvent());
        }

        return $self;
    }

    public function details(): array
    {
        return [
            'id' => $this->id->value()->toString(),
            'goals' => array_map(function (Goal $goal): array {
                return [
                    'id' => $goal->number(),
                    'scoredAt' => [
                        'min' => $goal->scoredAt()->min(),
                        'sec' => $goal->scoredAt()->sec(),
                    ],
                    'scorer' => [
                        'team' => $goal->scorer()->team(),
                        'position' => $goal->scorer()->position(),
                    ],
                ];
            }, $this->scoredGoals),
            'players' => [
                'blue' => [
                    'back' => [
                        'id' => $this->teamBlue->back(),
                        'name' => $this->teamBlue->back(),
                    ],
                    'front' => [
                        'id' => $this->teamBlue->front(),
                        'name' => $this->teamBlue->front(),
                    ],
                ],
                'red' => [
                    'back' => [
                        'id' => $this->teamRed->back(),
                        'name' => $this->teamRed->back(),
                    ],
                    'front' => [
                        'id' => $this->teamRed->front(),
                        'name' => $this->teamRed->front(),
                    ],
                ],
            ],
        ];
    }
}
