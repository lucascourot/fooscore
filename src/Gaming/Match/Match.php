<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

use DateTimeImmutable;

/**
 * Match aggregate root
 */
final class Match
{
    use EventSourcedAggregate;

    /**
     * @var MatchId
     */
    private $id;

    /**
     * @var Goal[]
     */
    private $scoredGoals = [];

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

    public static function start(MatchId $matchId, TeamBlue $teamBlue, TeamRed $teamRed, Clock $clock): self
    {
        $self = new self();
        $self->recordThat(new MatchWasStarted($matchId, $teamBlue, $teamRed, $clock->now()));

        return $self;
    }

    public function scoreGoal(Scorer $scorer, Clock $clock): self
    {
        $this->recordThat(new GoalWasScored(
            new Goal(
                count($this->scoredGoals) + 1,
                $scorer,
                ScoredAt::fromDifference($this->startedAt, $clock->now())
            )
        ));

        return $this;
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

    public function id(): MatchId
    {
        return $this->id;
    }

    public function scoredGoals(): array
    {
        return $this->scoredGoals;
    }

    public function teamBlue(): TeamBlue
    {
        return $this->teamBlue;
    }

    public function teamRed(): TeamRed
    {
        return $this->teamRed;
    }
}
