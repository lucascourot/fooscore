<?php

declare(strict_types=1);

namespace Fooscore\Tests\Integration\Ranking;

use Fooscore\Ranking\Infrastructure\PlayerRepositoryJson;
use Fooscore\Ranking\Player;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group integration
 */
class PlayerRepositoryJsonTest extends KernelTestCase
{
    public function testShouldReadFromJson(): void
    {
        // Given
        $adapter = new PlayerRepositoryJson(__DIR__.'/ranking-sample.json');

        // When
        $player = $adapter->get('foo');

        // Then
        static::assertEquals('foo', $player->id());
    }

    public function testShouldPersistAndReadNewScore(): void
    {
        $scoreFile = __DIR__.'/../../../var/tmp.json';
        file_put_contents($scoreFile, '{"johndoe": 1250}');

        // Given
        $adapter = new PlayerRepositoryJson($scoreFile);
        $player = new Player('johndoe', 1200);

        // When
        $resultSave = $adapter->save($player);

        // Then
        static::assertTrue($resultSave);

        // When
        $resultRead = $adapter->get('johndoe');

        // Then
        static::assertEquals(1200, $resultRead->score());

        unlink($scoreFile);
    }

    public function testShouldGetDefaultScoreIfNewPlayer(): void
    {
        // Given
        $adapter = new PlayerRepositoryJson(__DIR__.'/ranking-sample.json');

        // When
        $player = $adapter->get('bar');

        // Then
        static::assertEquals(0, $player->score());
    }

    public function testShouldThrowExceptionWhenNoFile(): void
    {
        static::expectException(\RuntimeException::class);

        // Given
        $adapter = new PlayerRepositoryJson('/path/not-exist.json');

        // When
        $adapter->get('foo');

        // Then
    }
}
