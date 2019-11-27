<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Gaming;

use DateTimeImmutable;
use Fooscore\Gaming\Match\MatchId;
use Fooscore\Gaming\Match\MatchIdGenerator;
use Fooscore\Gaming\Match\MatchRepository;
use Fooscore\Gaming\Match\MatchWasStarted;
use Fooscore\Gaming\Match\StartMatch;
use Fooscore\Gaming\Match\VersionedEvent;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @group unit
 *
 * In order to score goals
 * As a referee
 * I want to start a match
 */
class StartMatchTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testShouldStartMatch() : void
    {
        // Given
        $teamBlue = FakeTeam::blue('a', 'b');
        $teamRed = FakeTeam::red('c', 'd');
        $matchId = new MatchId(Uuid::fromString('6df9c8af-afeb-4422-ac60-5f271c738d76'));
        $matchIdGenerator = Mockery::mock(MatchIdGenerator::class, ['generate' => $matchId]);
        $matchRepository = Mockery::spy(MatchRepository::class);
        $startedAt = new DateTimeImmutable('2000-01-01 00:00:00');
        $fixedClock = new FixedClock($startedAt);

        // When
        $startMatchUseCase = new StartMatch($matchIdGenerator, $matchRepository, $fixedClock);
        $match = $startMatchUseCase($teamBlue, $teamRed);

        // Then
        self::assertEquals([
            new VersionedEvent(1, new MatchWasStarted($matchId, $teamBlue, $teamRed, $startedAt)),
        ], $match->recordedEvents());
        self::assertSame($matchId->value()->toString(), $match->id()->value()->toString());
        $matchRepository->shouldHaveReceived(null)->save($match)->once();
    }
}
