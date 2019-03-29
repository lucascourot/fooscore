<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Gaming;

use DateInterval;
use DateTimeImmutable;
use Fooscore\Gaming\Match\Goal;
use Fooscore\Gaming\Match\GoalWasAccumulated;
use Fooscore\Gaming\Match\GoalWasScored;
use Fooscore\Gaming\Match\Match;
use Fooscore\Gaming\Match\MatchAlreadyWon;
use Fooscore\Gaming\Match\MatchId;
use Fooscore\Gaming\Match\MatchRepository;
use Fooscore\Gaming\Match\MatchWasStarted;
use Fooscore\Gaming\Match\MatchWasWon;
use Fooscore\Gaming\Match\ScoredAt;
use Fooscore\Gaming\Match\ScoreMiddlefieldGoal;
use Fooscore\Gaming\Match\Scorer;
use Fooscore\Gaming\Match\VersionedEvent;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @group unit
 *
 * In order to play fair
 * As a referee
 * I want to not score middlefield goals directly
 */
class ScoreMiddlefieldGoalTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testShouldAccumulateGoalWhenScoredWithMiddlefieldPlayer() : void
    {
        // Given
        $matchId = new MatchId(Uuid::fromString('6df9c8af-afeb-4422-ac60-5f271c738d76'));

        $startedAt = new DateTimeImmutable('2000-01-01 00:00:00');
        $fixedClock = new FixedClock($startedAt);
        $teamBlue = FakeTeam::blue('a', 'b');
        $teamRed = FakeTeam::red('c', 'd');

        $matchRepository = Mockery::spy(MatchRepository::class);
        $matchRepository->allows('get')->with($matchId)->andReturns(
            Match::reconstituteFromHistory([
                new VersionedEvent(1, new MatchWasStarted($matchId, $teamBlue, $teamRed, $startedAt)),
            ])
        );

        // When
        $scoreMiddlefieldGoalUseCase = new ScoreMiddlefieldGoal($matchRepository, $fixedClock);
        $scorer = Scorer::fromTeamAndPosition('blue', 'back');
        $fixedClock->tick($startedAt->add(new DateInterval('PT1M30S')));
        $match = $scoreMiddlefieldGoalUseCase->scoreMiddlefieldGoal($matchId, $scorer);

        // Then
        self::assertEquals([
            new VersionedEvent(2, new GoalWasAccumulated(new Goal(1, $scorer, new ScoredAt(90)))),
        ], $match->recordedEvents());
        $matchRepository->shouldHaveReceived()->save($match)->once();
    }

    public function testShouldNotAccumulateGoalAfterMatchHasBeenWon() : void
    {
        $this->expectException(MatchAlreadyWon::class);

        // Given
        $matchId = new MatchId(Uuid::fromString('6df9c8af-afeb-4422-ac60-5f271c738d76'));
        $scorer = Scorer::fromTeamAndPosition('blue', 'back');

        $startedAt = new DateTimeImmutable('2000-01-01 00:00:00');
        $fixedClock = new FixedClock($startedAt);
        $teamBlue = FakeTeam::blue('a', 'b');
        $teamRed = FakeTeam::red('c', 'd');

        $matchRepository = Mockery::spy(MatchRepository::class);
        $matchRepository->allows('get')->with($matchId)->andReturns(
            Match::reconstituteFromHistory([
                new VersionedEvent(1, new MatchWasStarted($matchId, $teamBlue, $teamRed, $startedAt)),
                new VersionedEvent(2, new GoalWasScored(new Goal(1, $scorer, new ScoredAt(0)))),
                new VersionedEvent(3, new GoalWasScored(new Goal(2, $scorer, new ScoredAt(0)))),
                new VersionedEvent(4, new GoalWasScored(new Goal(3, $scorer, new ScoredAt(0)))),
                new VersionedEvent(5, new GoalWasScored(new Goal(4, $scorer, new ScoredAt(0)))),
                new VersionedEvent(6, new GoalWasScored(new Goal(5, $scorer, new ScoredAt(0)))),
                new VersionedEvent(7, new GoalWasScored(new Goal(6, $scorer, new ScoredAt(0)))),
                new VersionedEvent(8, new GoalWasScored(new Goal(7, $scorer, new ScoredAt(0)))),
                new VersionedEvent(9, new GoalWasScored(new Goal(8, $scorer, new ScoredAt(0)))),
                new VersionedEvent(10, new GoalWasScored(new Goal(9, $scorer, new ScoredAt(0)))),
                new VersionedEvent(11, new GoalWasScored(new Goal(10, $scorer, new ScoredAt(0)))),
                new VersionedEvent(12, new MatchWasWon('blue')),
            ])
        );

        // When
        $scoreGoalUseCase = new ScoreMiddlefieldGoal($matchRepository, $fixedClock);
        $scoreGoalUseCase->scoreMiddlefieldGoal($matchId, $scorer);
    }
}
