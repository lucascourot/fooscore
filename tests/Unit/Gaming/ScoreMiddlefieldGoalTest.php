<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Gaming;

use Fooscore\Gaming\Match\Goal;
use Fooscore\Gaming\Match\GoalWasAccumulated;
use Fooscore\Gaming\Match\MatchAlreadyWon;
use Fooscore\Gaming\Match\ScoredAt;
use Fooscore\Gaming\Match\ScoreMiddlefieldGoal;
use Fooscore\Gaming\Match\Scorer;
use Fooscore\Gaming\Match\VersionedEvent;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

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
        $clock = new ClockFrozenIn2010();
        $matchListWithOneStartedMatch = new MatchListWithOneStartedMatch($clock);

        // When
        $clock->add1Min30Sec();
        $scoreMiddlefieldGoal = new ScoreMiddlefieldGoal($matchListWithOneStartedMatch, $clock);
        $scorer = Scorer::fromTeamAndPosition('blue', 'back');
        $accumulatedGoal = $scoreMiddlefieldGoal($matchListWithOneStartedMatch->matchId(), $scorer);

        // Then
        self::assertEquals(new GoalWasAccumulated(new Goal(1, $scorer, new ScoredAt(90))), $accumulatedGoal);
        self::assertEquals(
            [
                new VersionedEvent(2, new GoalWasAccumulated(new Goal(1, $scorer, new ScoredAt(90)))),
            ],
            $matchListWithOneStartedMatch->savedMatch()->recordedEvents()
        );
    }

    public function testShouldNotAccumulateGoalAfterMatchHasBeenWon() : void
    {
        $this->expectException(MatchAlreadyWon::class);

        // Given
        $clock = new ClockFrozenIn2010();
        $matchListWithOneFinishedMatch = new MatchListWithOneFinishedMatch($clock);

        // When
        $scoreMiddlefieldGoalUseCase = new ScoreMiddlefieldGoal($matchListWithOneFinishedMatch, $clock);
        $scoreMiddlefieldGoalUseCase(
            $matchListWithOneFinishedMatch->matchId(),
            Scorer::fromTeamAndPosition('blue', 'back')
        );
    }
}
