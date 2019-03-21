<?php

declare(strict_types=1);

namespace Fooscore\Tests\Property\Ranking;

use Eris\Generator;
use Eris\TestTrait;
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
 * @group property
 */
class UpdateEloScoreTest extends TestCase
{
    use MockeryPHPUnitIntegration;
    use TestTrait;

    public function testScoreShouldIncreaseForWinnersAndDecreaseForLosers() : void
    {
        ($this->forAll(
            Generator\choose(0, 2400),
            Generator\choose(0, 2400),
            Generator\choose(0, 2400),
            Generator\choose(0, 2400)
        ))(static function ($w1, $w2, $l1, $l2) : void {
            // Given
            $matchResult = new MatchResult(
                new WinningTeam('w1', 'w2'),
                new LosingTeam('l1', 'l2')
            );

            $eloScoresRepository = Mockery::spy(EloScoresRepository::class);
            $eloScoresRepository->allows('get')->with($matchResult)->andReturns(new EloScores($matchResult, [
                'w1' => $w1,
                'w2' => $w2,
                'l1' => $l1,
                'l2' => $l2,
            ]));

            // When
            $updateEloScore = new UpdateEloScore($eloScoresRepository);
            $updatedEloScores = $updateEloScore->updatePlayersScores($matchResult);

            // Then
            $eloScoresRepository->shouldHaveReceived()->save($updatedEloScores)->once();
            self::assertGreaterThanOrEqual($w1, $updatedEloScores->getScoreForPlayerId('w1'));
            self::assertGreaterThanOrEqual($w2, $updatedEloScores->getScoreForPlayerId('w2'));
            self::assertLessThanOrEqual($l1, $updatedEloScores->getScoreForPlayerId('l1'));
            self::assertLessThanOrEqual($l2, $updatedEloScores->getScoreForPlayerId('l2'));
        });
    }

    public function testPlayersCannotGainOrLooseMoreThan50Points() : void
    {
        ($this->forAll(
            Generator\choose(0, 2400),
            Generator\choose(0, 2400),
            Generator\choose(0, 2400),
            Generator\choose(0, 2400)
        ))(static function ($w1, $w2, $l1, $l2) : void {
            // Given
            $matchResult = new MatchResult(
                new WinningTeam('w1', 'w2'),
                new LosingTeam('l1', 'l2')
            );

            $eloScoresRepository = Mockery::spy(EloScoresRepository::class);
            $eloScoresRepository->allows('get')->with($matchResult)->andReturns(new EloScores($matchResult, [
                'w1' => $w1,
                'w2' => $w2,
                'l1' => $l1,
                'l2' => $l2,
            ]));

            // When
            $updateEloScore = new UpdateEloScore($eloScoresRepository);
            $updatedEloScores = $updateEloScore->updatePlayersScores($matchResult);

            // Then
            $eloScoresRepository->shouldHaveReceived()->save($updatedEloScores)->once();
            self::assertLessThanOrEqual(50, $updatedEloScores->getScoreForPlayerId('w1') - $w1);
            self::assertLessThanOrEqual(50, $updatedEloScores->getScoreForPlayerId('w2') - $w2);
            self::assertLessThanOrEqual(50, $l1 - $updatedEloScores->getScoreForPlayerId('l1'));
            self::assertLessThanOrEqual(50, $l2 - $updatedEloScores->getScoreForPlayerId('l2'));
        });
    }

    public function testLosersCannotHaveNegativeScore() : void
    {
        ($this->forAll(
            Generator\choose(0, 2400),
            Generator\choose(0, 2400),
            Generator\choose(0, 2400),
            Generator\choose(0, 2400)
        ))(static function ($w1, $w2, $l1, $l2) : void {
            // Given
            $matchResult = new MatchResult(
                new WinningTeam('w1', 'w2'),
                new LosingTeam('l1', 'l2')
            );

            $eloScoresRepository = Mockery::spy(EloScoresRepository::class);
            $eloScoresRepository->allows('get')->with($matchResult)->andReturns(new EloScores($matchResult, [
                'w1' => $w1,
                'w2' => $w2,
                'l1' => $l1,
                'l2' => $l2,
            ]));

            // When
            $updateEloScore = new UpdateEloScore($eloScoresRepository);
            $updatedEloScores = $updateEloScore->updatePlayersScores($matchResult);

            // Then
            $eloScoresRepository->shouldHaveReceived()->save($updatedEloScores)->once();
            self::assertGreaterThanOrEqual(0, $updatedEloScores->getScoreForPlayerId('l1'));
            self::assertGreaterThanOrEqual(0, $updatedEloScores->getScoreForPlayerId('l2'));
        });
    }
}
