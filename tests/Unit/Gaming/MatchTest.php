<?php

namespace Fooscore\Tests\Unit\Gaming;

use Fooscore\Gaming\DomainEvent;
use Fooscore\Gaming\GoalWasScored;
use Fooscore\Gaming\Match;
use Fooscore\Gaming\MatchId;
use Fooscore\Gaming\MatchWasStarted;
use Fooscore\Gaming\Scorer;
use Fooscore\Gaming\TeamBlue;
use Fooscore\Gaming\TeamRed;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @group unit
 *
 * Event sourced aggregate
 */
class MatchTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testShouldBeConstructedFromHistory(): void
    {
        $teamBlue = new TeamBlue('a', 'b');
        $teamRed = new TeamRed('c', 'd');
        $matchId = new MatchId(Uuid::fromString('6df9c8af-afeb-4422-ac60-5f271c738d76'));

        $match = Match::reconstituteFromHistory([
            new MatchWasStarted($matchId, $teamBlue, $teamRed),
            new GoalWasScored(Scorer::fromTeamAndPosition('blue', 'back')),
        ]);

        self::assertCount(0, $match->getRecordedEvents());
    }

    public function testShouldNotAcceptUnknownDomainEvents(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Match::reconstituteFromHistory([
            new class() implements DomainEvent {
            },
        ]);
    }
}
