<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

use DateTimeImmutable;
use InvalidArgumentException;
use function count;
use function get_class;
use function sprintf;

final class Match extends EventSourcedAggregateRoot
{
    private const MAX_SCORE = 10;

    /** @var MatchId */
    private $id;

    /** @var Goal[] */
    private $accumulatedGoals = [];

    /** @var TeamBlue */
    private $teamBlue;

    /** @var TeamRed */
    private $teamRed;

    /** @var DateTimeImmutable */
    private $startedAt;

    /** @var int */
    private $scoreBlue = 0;

    /** @var int */
    private $scoreRed = 0;

    /** @var int */
    private $lastGoalNumber = 0;

    /** @var bool */
    private $isWon = false;

    public function id() : MatchId
    {
        return $this->id;
    }

    public function teamBlue() : TeamBlue
    {
        return $this->teamBlue;
    }

    public function teamRed() : TeamRed
    {
        return $this->teamRed;
    }

    public static function start(MatchId $matchId, TeamBlue $teamBlue, TeamRed $teamRed, Clock $clock) : self
    {
        $self = new self();
        $self->recordThat(new MatchWasStarted($matchId, $teamBlue, $teamRed, $clock->now()));

        return $self;
    }

    public function scoreGoal(Scorer $scorer, Clock $clock) : self
    {
        if ($this->isWon) {
            throw new MatchAlreadyWon('Match has already been won.');
        }

        $accumulatedGoalsCount = count($this->accumulatedGoals);
        $scoredAt = ScoredAt::fromDifference($this->startedAt, $clock->now());

        if ($accumulatedGoalsCount > 0) {
            $this->recordThat(new MiddlefieldGoalsWereValidatedByRegularGoal(
                new Goal($this->lastGoalNumber + 1, $scorer, $scoredAt),
                $accumulatedGoalsCount + 1
            ));
        } else {
            $this->recordThat(new GoalWasScored(
                new Goal($this->lastGoalNumber + 1, $scorer, $scoredAt)
            ));
        }

        if ($this->scoreBlue >= self::MAX_SCORE || $this->scoreRed >= self::MAX_SCORE) {
            $this->recordThat(new MatchWasWon($scorer->team()));
        }

        return $this;
    }

    public function scoreMiddlefieldGoal(Scorer $scorer, Clock $clock) : MiddlefieldGoalWasScored
    {
        if ($this->isWon) {
            throw new MatchAlreadyWon('Match has already been won.');
        }

        $goalWasAccumulated = new MiddlefieldGoalWasScored(
            new Goal(
                $this->lastGoalNumber + 1,
                $scorer,
                ScoredAt::fromDifference($this->startedAt, $clock->now())
            )
        );

        $this->recordThat($goalWasAccumulated);

        return $goalWasAccumulated;
    }

    protected function apply(DomainEvent $event) : void
    {
        if ($event instanceof MatchWasStarted) {
            $this->id = $event->matchId();
            $this->teamBlue = $event->teamBlue();
            $this->teamRed = $event->teamRed();
            $this->startedAt = $event->startedAt();

            return;
        }

        if ($event instanceof GoalWasScored) {
            $this->accumulatedGoals = [];
            $this->lastGoalNumber = $event->goal()->number();

            if ($event->goal()->scorer()->team() === 'blue') {
                $this->scoreBlue++;
            }
            if ($event->goal()->scorer()->team() === 'red') {
                $this->scoreRed++;
            }

            return;
        }

        if ($event instanceof MiddlefieldGoalsWereValidatedByRegularGoal) {
            $this->accumulatedGoals = [];
            $this->lastGoalNumber = $event->goal()->number();

            if ($event->goal()->scorer()->team() === 'blue') {
                $this->scoreBlue += $event->numberOfGoalsToValidate();
            }
            if ($event->goal()->scorer()->team() === 'red') {
                $this->scoreRed += $event->numberOfGoalsToValidate();
            }

            return;
        }

        if ($event instanceof MiddlefieldGoalWasScored) {
            $this->accumulatedGoals[] = $event->goal();
            $this->lastGoalNumber = $event->goal()->number();

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
}
