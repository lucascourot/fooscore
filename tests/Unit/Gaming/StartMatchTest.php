<?php

namespace Fooscore\Tests\Unit\Gaming;

use Fooscore\Gaming\Gaming;
use Fooscore\Gaming\TeamBlue;
use Fooscore\Gaming\TeamRed;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

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
        $teamRed = new TeamRed('a', 'b');

        // When
        $gaming = new Gaming();
        $matchId = $gaming->startMatch($teamBlue, $teamRed);

        // Then
        self::assertSame(1, $matchId);
    }
}
