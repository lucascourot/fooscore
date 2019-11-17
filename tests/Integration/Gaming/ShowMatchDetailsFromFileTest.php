<?php

declare(strict_types=1);

namespace Fooscore\Tests\Integration\Gaming;

use Fooscore\Gaming\Infrastructure\ShowMatchDetailsFromFile;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @group integration
 */
class ShowMatchDetailsFromFileTest extends TestCase
{
    /** @var string */
    private $testMatchId = '6df9c8af-afeb-4422-ac60-5f271c738d76';

    /** @var string */
    private $dir;

    protected function setUp() : void
    {
        $this->dir = __DIR__ . '/match-view/';
    }

    public function testShouldShowMatchDetails() : void
    {
        // Given
        $showMatchDetails = new ShowMatchDetailsFromFile($this->dir);

        // When
        $details = $showMatchDetails($this->testMatchId);

        // Then
        self::assertSame($details, [
            'id' => '6df9c8af-afeb-4422-ac60-5f271c738d76',
            'isFinished' => false,
            'score' => [
                'blue' => 4,
                'red' => 0,
            ],
            'goals' => [
                [
                    'id' => 1,
                    'type' => 'regular',
                    'accumulated' => [],
                    'scoredAt' => [
                        'min' => 0,
                        'sec' => 4,
                    ],
                    'scorer' => [
                        'team' => 'blue',
                        'position' => 'back',
                    ],
                ],
                [
                    'id' => 2,
                    'type' => 'middlefield',
                    'scoredAt' => [
                        'min' => 1,
                        'sec' => 4,
                    ],
                    'scorer' => [
                        'team' => 'blue',
                        'position' => 'front',
                    ],
                ],
                [
                    'id' => 3,
                    'type' => 'middlefield',
                    'scoredAt' => [
                        'min' => 2,
                        'sec' => 4,
                    ],
                    'scorer' => [
                        'team' => 'blue',
                        'position' => 'front',
                    ],
                ],
                [
                    'id' => 4,
                    'type' => 'regular',
                    'accumulated' => [2, 3],
                    'scoredAt' => [
                        'min' => 3,
                        'sec' => 4,
                    ],
                    'scorer' => [
                        'team' => 'blue',
                        'position' => 'front',
                    ],
                ],
            ],
            'players' => [
                'blue' => [
                    'back' => [
                        'id' => 'a',
                        'name' => 'a',
                    ],
                    'front' => [
                        'id' => 'b',
                        'name' => 'b',
                    ],
                ],
                'red' => [
                    'back' => [
                        'id' => 'c',
                        'name' => 'c',
                    ],
                    'front' => [
                        'id' => 'd',
                        'name' => 'd',
                    ],
                ],
            ],
        ]);
    }

    public function testCannotReadProjection() : void
    {
        $this->expectException(RuntimeException::class);

        // Given
        $showMatchDetails = new ShowMatchDetailsFromFile('unknowndir');

        // When
        $showMatchDetails($this->testMatchId);
    }
}
