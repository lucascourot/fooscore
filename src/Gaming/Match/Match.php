<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

/**
 * Match aggregate root
 */
final class Match
{
    /**
     * @var array
     */
    private $recordedEvents = [];

    /**
     * @var MatchId
     */
    private $id;

    /**
     * @var array
     */
    private $goals = [];

    public static function start(MatchId $matchId, TeamBlue $teamBlue, TeamRed $teamRed): self
    {
        $self = new self();
        $self->recordThat(new MatchWasStarted($matchId, $teamBlue, $teamRed));

        return $self;
    }

    public function id(): MatchId
    {
        return $this->id;
    }

    public function scoreGoal(Scorer $scorer): self
    {
        $this->recordThat(new GoalWasScored($scorer));

        return $this;
    }

    public function getRecordedEvents(): array
    {
        return $this->recordedEvents;
    }

    private function recordThat(DomainEvent $event): void
    {
        $this->recordedEvents[] = $event;
        $this->apply($event);
    }

    private function apply(DomainEvent $event): void
    {
        if ($event instanceof GoalWasScored) {
            $this->goals[] = $event->getScorer();

            return;
        }

        if ($event instanceof MatchWasStarted) {
            $this->id = $event->getMatchId();

            return;
        }

        throw new \InvalidArgumentException(
            sprintf('Unknown domain event "%s"', get_class($event))
        );
    }

    /**
     * @param DomainEvent[] $domainEvents
     */
    public static function reconstituteFromHistory(array $domainEvents): self
    {
        $self = new self();

        foreach ($domainEvents as $domainEvent) {
            $self->apply($domainEvent);
        }

        return $self;
    }
}
