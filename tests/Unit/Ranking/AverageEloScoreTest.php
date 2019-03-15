<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Ranking;

use Fooscore\Ranking\AverageEloScore;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class AverageEloScoreTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testShouldCalculateAverageForTeam(): void
    {
        $average = AverageEloScore::team(10, 11);

        self::assertSame(11, $average);
    }
}
