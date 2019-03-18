<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Gaming;

use DateTimeImmutable;
use Fooscore\Gaming\Match\DomainEvent;
use Fooscore\Gaming\Match\Goal;
use Fooscore\Gaming\Match\GoalWasScored;
use Fooscore\Gaming\Match\Match;
use Fooscore\Gaming\Match\MatchId;
use Fooscore\Gaming\Match\MatchWasStarted;
use Fooscore\Gaming\Match\ScoredAt;
use Fooscore\Gaming\Match\Scorer;
use Fooscore\Gaming\Match\VersionedEvent;
use InvalidArgumentException;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @group unit
 */
class EventSourcedMatchTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testShouldBeConstructedFromHistory() : void
    {
        $teamBlue = FakeTeam::blue('a', 'b');
        $teamRed = FakeTeam::red('a', 'b');
        $matchId = new MatchId(Uuid::fromString('6df9c8af-afeb-4422-ac60-5f271c738d76'));

        $match = Match::reconstituteFromHistory([
            new VersionedEvent(1, new MatchWasStarted(
                $matchId,
                $teamBlue,
                $teamRed,
                new DateTimeImmutable('2000-01-01 00:00:00')
            )),
            new VersionedEvent(2, new GoalWasScored(
                new Goal(1, Scorer::fromTeamAndPosition('blue', 'back'), new ScoredAt(90))
            )),
        ]);

        self::assertCount(0, $match->recordedEvents());
    }

    public function testShouldNotAcceptUnknownDomainEvents() : void
    {
        $this->expectException(InvalidArgumentException::class);

        Match::reconstituteFromHistory([
            new VersionedEvent(
                1,
                new class() implements DomainEvent {
                    public static function eventName() : string
                    {
                        return 'error';
                    }

                    /**
                     * {@inheritdoc}
                     */
                    public static function fromEventDataArray(array $eventData) : DomainEvent
                    {
                        return new self();
                    }

                    /**
                     * {@inheritdoc}
                     */
                    public function eventDataAsArray() : array
                    {
                        return [];
                    }
                }
            ),
        ]);
    }
}
