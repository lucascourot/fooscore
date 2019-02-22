<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Gaming;

use Fooscore\Gaming\Match\{
    Goal, GoalWasScored, Match, MatchId, MatchRepository, MatchWasStarted, Scorer, TeamBlue, TeamRed, UseCaseScoreGoal, VersionedEvent
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
        $matchRepository = Mockery::spy(MatchRepository::class);
        $matchRepository->allows('get')->with($matchId)->andReturns(
            Match::reconstituteFromHistory([
                new VersionedEvent(1, new MatchWasStarted($matchId, new TeamBlue('a', 'b'), new TeamRed('c', 'd'))),
            ])
        );

        // When
        $scoreGoalUseCase = new UseCaseScoreGoal($matchRepository);
        $scorer = Scorer::fromTeamAndPosition('blue', 'back');
        $match = $scoreGoalUseCase->scoreGoal($matchId, $scorer);

        // Then
        self::assertEquals([
            new VersionedEvent(2, new GoalWasScored(new Goal(1, $scorer))),
        ], $match->recordedEvents());
        self::assertEquals(new Goal(1, $scorer), $match->lastScoredGoal());
        self::assertEquals(1, $match->lastScoredGoal()->number());
        $matchRepository->shouldHaveReceived()->save($match)->once();
    }

    public function testShouldScoreIncrementGoalIds(): void
    {
        // Given
        $matchId = new MatchId(Uuid::fromString('6df9c8af-afeb-4422-ac60-5f271c738d76'));
        $matchRepository = Mockery::spy(MatchRepository::class);
        $matchRepository->allows('get')->with($matchId)->andReturns(
            Match::reconstituteFromHistory([
                new VersionedEvent(1, new MatchWasStarted($matchId, new TeamBlue('a', 'b'), new TeamRed('c', 'd'))),
            ])
        );

        // When
        $scoreGoalUseCase = new UseCaseScoreGoal($matchRepository);
        $scorer = Scorer::fromTeamAndPosition('blue', 'back');
        $scoreGoalUseCase->scoreGoal($matchId, $scorer);
        $match = $scoreGoalUseCase->scoreGoal($matchId, $scorer);

        // Then
        self::assertEquals([
            new VersionedEvent(2, new GoalWasScored(new Goal(1, $scorer))),
            new VersionedEvent(3, new GoalWasScored(new Goal(2, $scorer))),
        ], $match->recordedEvents());
        self::assertEquals(new Goal(2, $scorer), $match->lastScoredGoal());
        self::assertEquals(2, $match->lastScoredGoal()->number());
        $matchRepository->shouldHaveReceived()->save($match)->twice();
    }
}
