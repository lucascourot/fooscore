<?php

declare(strict_types=1);

namespace Fooscore\Tests\Integration\Gaming;

use Fooscore\Gaming\Infrastructure\MatchIdGeneratorUuidv4;
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
        self::assertSame(4, $matchId->value()->getVersion());
    }
}
