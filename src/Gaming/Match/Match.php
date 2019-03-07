<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Match aggregate root
 */
final class Match
{
    private const MAX_SCORE = 10;

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

    /**
     * @var int
     */
    private $scoreBlue = 0;

    /**
     * @var int
     */
    private $scoreRed = 0;

    /**
     * @var bool
     */
    private $isWon = false;

    public static function start(MatchId $matchId, TeamBlue $teamBlue, TeamRed $teamRed, Clock $clock): self
    {
        $self = new self();
        $self->recordThat(new MatchWasStarted($matchId, $teamBlue, $teamRed, $clock->now()));

        return $self;
    }

    public function scoreGoal(Scorer $scorer, Clock $clock): self
    {
        if ($this->isWon) {
            throw new MatchAlreadyWon('Match has already been won.');
        }

        $this->recordThat(new GoalWasScored(
            new Goal(
                count($this->scoredGoals) + 1,
                $scorer,
                ScoredAt::fromDifference($this->startedAt, $clock->now())
            )
        ));

        if ($this->scoreBlue === self::MAX_SCORE || $this->scoreRed === self::MAX_SCORE) {
            $this->recordThat(new MatchWasWon($scorer->team()));
        }

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

            if ($event->goal()->scorer()->team() === 'blue') {
                $this->scoreBlue++;
            }
            if ($event->goal()->scorer()->team() === 'red') {
                $this->scoreRed++;
            }

            return;
        }

        if ($event instanceof MatchWasWon) {
            $this->isWon = true;

            return;
        }

        throw new InvalidArgumentException(
            sprintf('Unknown domain event "%s"', get_class($event))
        );
    }

    public function id(): MatchId
    {
        return $this->id;
    }
}
