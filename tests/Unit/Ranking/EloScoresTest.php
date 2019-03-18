<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Ranking;

use Fooscore\Ranking\EloScores;
use Fooscore\Ranking\LosingTeam;
use Fooscore\Ranking\MatchResult;
use Fooscore\Ranking\WinningTeam;
use InvalidArgumentException;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class EloScoresTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testShouldNotGetScoreForNotExistingPlayer() : void
    {
        $this->expectException(InvalidArgumentException::class);

        $eloScores = new EloScores(
            new MatchResult(
                new WinningTeam('w1', 'w2'),
                new LosingTeam('l1', 'l2')
            ),
            ['a' => 1000]
        );

        $eloScores->getScoreForPlayerId('w1');
    }
}
