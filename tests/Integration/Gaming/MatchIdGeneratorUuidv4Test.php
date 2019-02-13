<?php

namespace Fooscore\Tests\Integration\Gaming;

use Fooscore\Adapters\Gaming\MatchIdGeneratorUuidv4;
use Fooscore\Gaming\MatchId;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

/**
 * @group integration
 */
class MatchIdGeneratorUuidv4Test extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testShouldGenerateUuidV4MatchId(): void
    {
        // Given
        $adapter = new MatchIdGeneratorUuidv4();

        // When
        $matchId = $adapter->generate();

        // Then
        self::assertInstanceOf(MatchId::class, $matchId);
        self::assertSame(4, $matchId->value()->getVersion());
    }
}
