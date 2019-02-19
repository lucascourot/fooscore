<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

/**
 * Match aggregate root
 */
final class Match
{
    use DisableConstruct;

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

    public static function start(MatchId $matchId, TeamBlue $teamBlue, TeamRed $teamRed): self
    {
        $self = new self();
        $self->recordThat(new MatchWasStarted($matchId, $teamBlue, $teamRed));

        return $self;
    }

    public function scoreGoal(Scorer $scorer): self
    {
        $this->recordThat(new GoalWasScored(new Goal(1, $scorer)));

        return $this;
    }

    public function id(): MatchId
    {
        return $this->id;
    }

    /**
     * @return Goal[]
     */
    public function scoredGoals(): array
    {
        return $this->scoredGoals;
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
        if ($event instanceof GoalWasScored) {
            $this->scoredGoals[] = $event->goal();

            return;
        }

        if ($event instanceof MatchWasStarted) {
            $this->id = $event->matchId();

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
}
