<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Ranking;

use Fooscore\Ranking\EloScores;
use Fooscore\Ranking\EloScoresRepository;
use Fooscore\Ranking\LosingTeam;
use Fooscore\Ranking\MatchResult;
use Fooscore\Ranking\UpdateEloScore;
use Fooscore\Ranking\WinningTeam;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 *
 * In order to rank best players
 * As a system
 * I want to update ELO scores
 */
class UpdateEloScoreTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testShouldUpdateEloScoreWithChallengerTeamWinning() : void
    {
        // Given
        $matchResult = new MatchResult(
            new WinningTeam('w1', 'w2'),
            new LosingTeam('l1', 'l2')
        );

        $eloScoresRepository = Mockery::spy(EloScoresRepository::class);
        $eloScoresRepository->allows('get')->with($matchResult)->andReturns(new EloScores($matchResult, [
            'w1' => 1495,
            'w2' => 1315,
            'l1' => 2069,
            'l2' => 1940,
        ]));

        // When
        $updateEloScore = new UpdateEloScore($eloScoresRepository);
        $updatedEloScores = $updateEloScore->updatePlayersScores($matchResult);

        // Then
        $eloScoresRepository->shouldHaveReceived(null)->save($updatedEloScores)->once();
        self::assertEquals(1535, $updatedEloScores->getScoreForPlayerId('w1'));
        self::assertEquals(1355, $updatedEloScores->getScoreForPlayerId('w2'));
        self::assertEquals(2029, $updatedEloScores->getScoreForPlayerId('l1'));
        self::assertEquals(1900, $updatedEloScores->getScoreForPlayerId('l2'));
    }

    public function testShouldUpdateEloScoreWithFavoriteTeamWinning() : void
    {
        // Given
        $matchResult = new MatchResult(
            new WinningTeam('w1', 'w2'),
            new LosingTeam('l1', 'l2')
        );

        $eloScoresRepository = Mockery::spy(EloScoresRepository::class);
        $eloScoresRepository->allows('get')->with($matchResult)->andReturns(new EloScores($matchResult, [
            'w1' => 2069,
            'w2' => 1940,
            'l1' => 1495,
            'l2' => 1315,
        ]));

        // When
        $updateEloScore = new UpdateEloScore($eloScoresRepository);
        $updatedEloScores = $updateEloScore->updatePlayersScores($matchResult);

        // Then
        $eloScoresRepository->shouldHaveReceived(null)->save($updatedEloScores)->once();
        self::assertEquals(2079, $updatedEloScores->getScoreForPlayerId('w1'));
        self::assertEquals(1950, $updatedEloScores->getScoreForPlayerId('w2'));
        self::assertEquals(1485, $updatedEloScores->getScoreForPlayerId('l1'));
        self::assertEquals(1305, $updatedEloScores->getScoreForPlayerId('l2'));
    }

    public function testShouldNotDecreaseLoserPlayerAScoreIfNull() : void
    {
        // Given
        $matchResult = new MatchResult(
            new WinningTeam('w1', 'w2'),
            new LosingTeam('l1', 'l2')
        );

        $eloScoresRepository = Mockery::spy(EloScoresRepository::class);
        $eloScoresRepository->allows('get')->with($matchResult)->andReturns(new EloScores($matchResult, [
            'w1' => 2069,
            'w2' => 1940,
            'l1' => 0,
            'l2' => 800,
        ]));

        // When
        $updateEloScore = new UpdateEloScore($eloScoresRepository);
        $updatedEloScores = $updateEloScore->updatePlayersScores($matchResult);

        // Then
        $eloScoresRepository->shouldHaveReceived(null)->save($updatedEloScores)->once();
        self::assertEquals(0, $updatedEloScores->getScoreForPlayerId('l1'));
    }

    public function testShouldNotDecreaseLoserPlayerBScoreIfNull() : void
    {
        // Given
        $matchResult = new MatchResult(
            new WinningTeam('w1', 'w2'),
            new LosingTeam('l1', 'l2')
        );

        $eloScoresRepository = Mockery::spy(EloScoresRepository::class);
        $eloScoresRepository->allows('get')->with($matchResult)->andReturns(new EloScores($matchResult, [
            'w1' => 2069,
            'w2' => 1940,
            'l1' => 900,
            'l2' => 0,
        ]));

        // When
        $updateEloScore = new UpdateEloScore($eloScoresRepository);
        $updatedEloScores = $updateEloScore->updatePlayersScores($matchResult);

        // Then
        $eloScoresRepository->shouldHaveReceived(null)->save($updatedEloScores)->once();
        self::assertEquals(0, $updatedEloScores->getScoreForPlayerId('l2'));
    }
}
