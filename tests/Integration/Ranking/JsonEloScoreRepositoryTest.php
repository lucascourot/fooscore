<?php

declare(strict_types=1);

namespace Fooscore\Tests\Integration\Ranking;

use Fooscore\Ranking\EloScores;
use Fooscore\Ranking\Infrastructure\JsonEloScoreRepository;
use Fooscore\Ranking\LosingTeam;
use Fooscore\Ranking\MatchResult;
use Fooscore\Ranking\WinningTeam;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use const DIRECTORY_SEPARATOR;
use function unlink;

/**
 * @group integration
 */
class JsonEloScoreRepositoryTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    /** @var string */
    private $dir;

    protected function setUp() : void
    {
        $kernel = self::bootKernel();
        $this->dir = $kernel->getProjectDir() . DIRECTORY_SEPARATOR . 'var/';
    }

    protected function tearDown() : void
    {
        parent::tearDown();

        @unlink($this->dir . 'ranking.json');
    }

    public function testShouldSaveWhenNoFileExists() : void
    {
        // Given
        $matchResult = new MatchResult(
            new WinningTeam('w1', 'w2'),
            new LosingTeam('l1', 'l2')
        );
        $repository = new JsonEloScoreRepository($this->dir);

        // When
        $repository->save(new EloScores($matchResult, [
            'w1' => 1000,
            'w2' => 1000,
            'l1' => 1000,
            'l2' => 1000,
        ]));

        // Then
        self::assertJsonStringEqualsJsonFile($this->dir . 'ranking.json', <<<JSON
{
    "w1": 1000,
    "w2": 1000,
    "l1": 1000,
    "l2": 1000
}
JSON
);
    }

    public function testShouldGetScoresForMatchPlayers() : void
    {
        // Given
        $matchResult = new MatchResult(
            new WinningTeam('w1', 'w2'),
            new LosingTeam('l1', 'l2')
        );
        $repository = new JsonEloScoreRepository($this->dir);
        $repository->save(new EloScores($matchResult, [
            'w1' => 2000,
            'w2' => 2000,
            'l1' => 2000,
            'l2' => 2000,
        ]));

        // When
        $eloScores = $repository->get($matchResult);

        // Then
        self::assertEquals(new EloScores($matchResult, [
            'w1' => 2000,
            'w2' => 2000,
            'l1' => 2000,
            'l2' => 2000,
        ]), $eloScores);
    }

    public function testShouldGetDefaultScoreIfNewPlayer() : void
    {
        // Given
        $matchResult = new MatchResult(
            new WinningTeam('w1', 'w2'),
            new LosingTeam('l1', 'l2')
        );
        $repository = new JsonEloScoreRepository($this->dir);

        // When
        $eloScores = $repository->get($matchResult);

        // Then
        self::assertEquals(new EloScores($matchResult, [
            'w1' => 1000,
            'w2' => 1000,
            'l1' => 1000,
            'l2' => 1000,
        ]), $eloScores);
    }
}
