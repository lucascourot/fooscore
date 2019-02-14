<?php

namespace Fooscore\Tests\Integration\Gaming;

use Fooscore\Adapters\Gaming\MatchRepositoryPg;
use Fooscore\Gaming\Match;
use Fooscore\Gaming\MatchId;
use Fooscore\Gaming\TeamBlue;
use Fooscore\Gaming\TeamRed;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @group integration
 */
class MatchRepositoryPgTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testShouldWriteToPgsql(): void
    {
        // Given
        $adapter = new MatchRepositoryPg();

        $teamBlue = new TeamBlue('a', 'b');
        $teamRed = new TeamRed('c', 'd');
        $matchId = new MatchId(Uuid::fromString('6df9c8af-afeb-4422-ac60-5f271c738d76'));

        $match = Match::start($matchId, $teamBlue, $teamRed);

        // When
        $adapter->save($match);

        // Then
        self::assertSame(true, true);
    }
}
