<?php

namespace Fooscore\Tests\Unit\Gaming;

use Fooscore\Gaming\Gaming;
use Fooscore\Gaming\MatchId;
use Fooscore\Gaming\MatchIdGenerator;
use Fooscore\Gaming\MatchRepository;
use Fooscore\Gaming\TeamBlue;
use Fooscore\Gaming\TeamRed;
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
        $id = $gaming->startMatch($teamBlue, $teamRed);

        // Then
        self::assertTrue($id->sameValueAs($matchId));
        $matchRepository
            ->shouldHaveReceived()
            ->save(Mockery::any())
            ->once();
    }
}
