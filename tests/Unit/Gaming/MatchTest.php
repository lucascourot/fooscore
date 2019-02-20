<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Gaming;

use Fooscore\Gaming\Match\{
    DomainEvent, Goal, GoalWasScored, Match, MatchId, MatchWasStarted, Scorer, TeamBlue, TeamRed, VersionedEvent
};
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
            new VersionedEvent(1, new MatchWasStarted($matchId, $teamBlue, $teamRed)),
            new VersionedEvent(2, new GoalWasScored(
                    new Goal(1, Scorer::fromTeamAndPosition('blue', 'back'))
                )
            ),
        ]);

        self::assertCount(0, $match->recordedEvents());
    }

    public function testShouldNotAcceptUnknownDomainEvents(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Match::reconstituteFromHistory([
            new VersionedEvent(
                1,
                new class() implements DomainEvent {
                    public static function eventName(): string
                    {
                        return 'error';
                    }

                    public static function fromEventDataArray(array $eventData): DomainEvent
                    {
                        return new self();
                    }

                    public function eventDataAsArray(): array
                    {
                        return [];
                    }
                }
            ),
        ]);
    }
}