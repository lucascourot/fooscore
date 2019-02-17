<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Gaming;

use Fooscore\Gaming\Gaming;
use Fooscore\Gaming\Match\GoalWasScored;
use Fooscore\Gaming\Match\Match;
use Fooscore\Gaming\Match\MatchId;
use Fooscore\Gaming\Match\MatchIdGenerator;
use Fooscore\Gaming\Match\MatchRepository;
use Fooscore\Gaming\Match\MatchWasStarted;
use Fooscore\Gaming\Match\Scorer;
use Fooscore\Gaming\Match\TeamBlue;
use Fooscore\Gaming\Match\TeamRed;
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
        $matchIdGenerator = Mockery::mock(MatchIdGenerator::class, [
            'generate' => $matchId,
        ]);
        $matchRepository = Mockery::spy(MatchRepository::class);
        $matchRepository->allows('get')->with($matchId)->andReturns(
            Match::reconstituteFromHistory([
                new MatchWasStarted($matchId, new TeamBlue('a', 'b'), new TeamRed('c', 'd')),
            ])
        );

        // When
        $gaming = new Gaming($matchIdGenerator, $matchRepository);
        $scorer = Scorer::fromTeamAndPosition('blue', 'back');
        $match = $gaming->scoreGoal($matchId, $scorer);

        // Then
        self::assertEquals([new GoalWasScored($scorer)], $match->getRecordedEvents());
        $matchRepository->shouldHaveReceived()->save($match)->once();
    }
}
