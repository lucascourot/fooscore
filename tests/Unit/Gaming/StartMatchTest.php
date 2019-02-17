<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Gaming;

use Fooscore\Gaming\Gaming;
use Fooscore\Gaming\Match\MatchId;
use Fooscore\Gaming\Match\MatchIdGenerator;
use Fooscore\Gaming\Match\MatchRepository;
use Fooscore\Gaming\Match\MatchWasStarted;
use Fooscore\Gaming\Match\TeamBlue;
use Fooscore\Gaming\Match\TeamRed;
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

    public function testShouldStartMatch(): void
    {
        // Given
        $teamBlue = new TeamBlue('a', 'b');
        $teamRed = new TeamRed('c', 'd');
        $matchId = new MatchId(Uuid::fromString('6df9c8af-afeb-4422-ac60-5f271c738d76'));
        $matchIdGenerator = Mockery::mock(MatchIdGenerator::class, [
            'generate' => $matchId,
        ]);
        $matchRepository = Mockery::spy(MatchRepository::class);

        // When
        $gaming = new Gaming($matchIdGenerator, $matchRepository);
        $match = $gaming->startMatch($teamBlue, $teamRed);

        // Then
        self::assertEquals([new MatchWasStarted($matchId, $teamBlue, $teamRed)], $match->getRecordedEvents());
        self::assertSame($matchId->value()->toString(), $match->id()->value()->toString());
        $matchRepository->shouldHaveReceived()->save($match)->once();
    }
}
