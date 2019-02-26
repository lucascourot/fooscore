<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Gaming;

use Fooscore\Gaming\Match\{
    DomainEvent, Goal, GoalWasScored, Match, MatchId, MatchWasStarted, ScoredAt, Scorer, TeamBlue, TeamRed, VersionedEvent
};
use Fooscore\Gaming\MatchDetails\MatchDetails;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @group unit
 */
class MatchDetailsTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testShouldThrowExceptionWhenFetchingLastGoalAndNoScoredGoalYet(): void
    {
        $this->expectException(\RuntimeException::class);

        $teamBlue = new TeamBlue('a', 'b');
        $teamRed = new TeamRed('c', 'd');
        $matchId = new MatchId(Uuid::fromString('6df9c8af-afeb-4422-ac60-5f271c738d76'));

        $match = Match::reconstituteFromHistory([
            new VersionedEvent(1, new MatchWasStarted($matchId, $teamBlue, $teamRed, new \DateTimeImmutable('2000-01-01 00:00:00'))),
        ]);

        MatchDetails::fromMatch($match)->lastScoredGoal();
    }

    public function testShouldReturnLastScoredGoal(): void
    {
        $teamBlue = new TeamBlue('a', 'b');
        $teamRed = new TeamRed('c', 'd');
        $matchId = new MatchId(Uuid::fromString('6df9c8af-afeb-4422-ac60-5f271c738d76'));

        $match = Match::reconstituteFromHistory([
            new VersionedEvent(1, new MatchWasStarted($matchId, $teamBlue, $teamRed, new \DateTimeImmutable('2000-01-01 00:00:00'))),
            new VersionedEvent(2, new GoalWasScored(
                    new Goal(1, Scorer::fromTeamAndPosition('blue', 'back'), new ScoredAt(90))
                )
            ),
            new VersionedEvent(3, new GoalWasScored(
                    new Goal(2, Scorer::fromTeamAndPosition('red', 'back'), new ScoredAt(90))
                )
            ),
        ]);

        self::assertEquals(
            new Goal(2, Scorer::fromTeamAndPosition('red', 'back'), new ScoredAt(90)),
            MatchDetails::fromMatch($match)->lastScoredGoal()
        );
    }
}
