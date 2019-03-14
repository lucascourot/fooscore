<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Ranking;

use Fooscore\Ranking\EloScore;
use Fooscore\Ranking\Player;
use Fooscore\Ranking\PlayerRepository;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class EloScoreTest extends TestCase
{
    public function testShouldUpdateEloScoreWithChallengerWinning(): void
    {
        // Given
        $winningPlayer1 = new Player('a1', 1601);
        $winningPlayer2 = new Player('a2', 2000);
        $losingPlayer1 = new Player('b1', 1905);
        $losingPlayer2 = new Player('b1', 2105);

        $playerRepository = $this->createMock(PlayerRepository::class);
        $playerRepository->method('get')
            ->willReturnOnConsecutiveCalls($winningPlayer1, $winningPlayer2, $losingPlayer1, $losingPlayer2);
        $playerRepository->expects(static::exactly(4))
            ->method('save');

        // When
        $eloScore = new EloScore($playerRepository);
        $eloScore->updateEloScore('a1', 'a2', 'a3', 'a4');

        // Then
        static::assertEquals(1616, $winningPlayer1->score());
        static::assertEquals(2015, $winningPlayer2->score());
        static::assertEquals(1890, $losingPlayer1->score());
        static::assertEquals(2090, $losingPlayer2->score());
    }

    public function testShouldUpdateEloScoreWithBestTeamWinning(): void
    {
        // Given
        $winningPlayer1 = new Player('b1', 1905);
        $winningPlayer2 = new Player('b1', 2105);
        $losingPlayer1 = new Player('a1', 1601);
        $losingPlayer2 = new Player('a2', 2000);

        $playerRepository = $this->createMock(PlayerRepository::class);
        $playerRepository->method('get')
            ->willReturnOnConsecutiveCalls($winningPlayer1, $winningPlayer2, $losingPlayer1, $losingPlayer2);
        $playerRepository->expects(static::exactly(4))
            ->method('save');

        // When
        $eloScore = new EloScore($playerRepository);
        $eloScore->updateEloScore('a1', 'a2', 'a3', 'a4');

        // Then
        static::assertEquals(1910, $winningPlayer1->score());
        static::assertEquals(2110, $winningPlayer2->score());
        static::assertEquals(1596, $losingPlayer1->score());
        static::assertEquals(1995, $losingPlayer2->score());
    }

    /**
     * 1601 + 2000 = 3601
     */
    public function testShouldUpdateEloScore2222(): void
    {
        // Given
        $winningPlayer1 = new Player('a1', 1602);
        $winningPlayer2 = new Player('a2', 2000);
        $losingPlayer1 = new Player('b1', 1905);
        $losingPlayer2 = new Player('b1', 2105);

        $playerRepository = $this->createMock(PlayerRepository::class);
        $playerRepository->method('get')
            ->willReturnOnConsecutiveCalls($winningPlayer1, $winningPlayer2, $losingPlayer1, $losingPlayer2);
        $playerRepository->expects(static::exactly(4))
            ->method('save');

        // When
        $eloScore = new EloScore($playerRepository);
        $eloScore->updateEloScore('a1', 'a2', 'a3', 'a4');

        // Then
        static::assertEquals(1617, $winningPlayer1->score());
        static::assertEquals(2015, $winningPlayer2->score());
        static::assertEquals(1890, $losingPlayer1->score());
        static::assertEquals(2090, $losingPlayer2->score());
    }

    /**
     * 1601 + 2000 = 3601
     */
    public function testShouldUpdateEloScore3333(): void
    {
        // Given
        $winningPlayer1 = new Player('a1', 1600);
        $winningPlayer2 = new Player('a2', 2000);
        $losingPlayer1 = new Player('b1', 1905);
        $losingPlayer2 = new Player('b1', 2105);

        $playerRepository = $this->createMock(PlayerRepository::class);
        $playerRepository->method('get')
            ->willReturnOnConsecutiveCalls($winningPlayer1, $winningPlayer2, $losingPlayer1, $losingPlayer2);
        $playerRepository->expects(static::exactly(4))
            ->method('save');

        // When
        $eloScore = new EloScore($playerRepository);
        $eloScore->updateEloScore('a1', 'a2', 'a3', 'a4');

        // Then
        static::assertEquals(1615, $winningPlayer1->score());
        static::assertEquals(2015, $winningPlayer2->score());
        static::assertEquals(1890, $losingPlayer1->score());
        static::assertEquals(2090, $losingPlayer2->score());
    }
}
