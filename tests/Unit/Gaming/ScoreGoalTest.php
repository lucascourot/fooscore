<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Gaming;

use DateInterval;
use Fooscore\Gaming\Match\{
    Goal,
    GoalWasScored,
    Match,
    MatchId,
    MatchRepository,
    MatchWasStarted,
    ScoreGoal,
    ScoredAt,
    Scorer,
    TeamBlue,
    TeamRed,
    VersionedEvent
};
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @group unit
 *
 * In order to see who won the match
 * As a referee
 * I want to score goals
 */
class ScoreGoalTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testShouldScoreRegularGoal(): void
    {
        // Given
        $matchId = new MatchId(Uuid::fromString('6df9c8af-afeb-4422-ac60-5f271c738d76'));

        $startedAt = new \DateTimeImmutable('2000-01-01 00:00:00');
        $fixedClock = new FixedClock($startedAt);

        $matchRepository = Mockery::spy(MatchRepository::class);
        $matchRepository->allows('get')->with($matchId)->andReturns(
            Match::reconstituteFromHistory([
                new VersionedEvent(1, new MatchWasStarted($matchId, new TeamBlue('a', 'b'), new TeamRed('c', 'd'), $startedAt)),
            ])
        );

        // When
        $scoreGoalUseCase = new ScoreGoal($matchRepository, $fixedClock);
        $scorer = Scorer::fromTeamAndPosition('blue', 'back');
        $fixedClock->tick($startedAt->add(new DateInterval('PT1M30S')));
        $match = $scoreGoalUseCase->scoreGoal($matchId, $scorer);

        // Then
        self::assertEquals([
            new VersionedEvent(2, new GoalWasScored(new Goal(1, $scorer, new ScoredAt(90)))),
        ], $match->recordedEvents());
        $matchRepository->shouldHaveReceived()->save($match)->once();
    }

    public function testShouldScoreIncrementGoalIds(): void
    {
        // Given
        $matchId = new MatchId(Uuid::fromString('6df9c8af-afeb-4422-ac60-5f271c738d76'));

        $startedAt = new \DateTimeImmutable('2000-01-01 00:00:00');
        $fixedClock = new FixedClock($startedAt);

        $matchRepository = Mockery::spy(MatchRepository::class);
        $matchRepository->allows('get')->with($matchId)->andReturns(
            Match::reconstituteFromHistory([
                new VersionedEvent(1, new MatchWasStarted($matchId, new TeamBlue('a', 'b'), new TeamRed('c', 'd'), $startedAt)),
            ])
        );

        // When
        $scoreGoalUseCase = new ScoreGoal($matchRepository, $fixedClock);
        $scorer = Scorer::fromTeamAndPosition('blue', 'back');
        $fixedClock->tick($startedAt->add(new DateInterval('PT1M30S')));
        $scoreGoalUseCase->scoreGoal($matchId, $scorer);
        $match = $scoreGoalUseCase->scoreGoal($matchId, $scorer);

        // Then
        self::assertEquals([
            new VersionedEvent(2, new GoalWasScored(new Goal(1, $scorer, new ScoredAt(90)))),
            new VersionedEvent(3, new GoalWasScored(new Goal(2, $scorer, new ScoredAt(90)))),
        ], $match->recordedEvents());
        $matchRepository->shouldHaveReceived()->save($match)->twice();
    }
}
