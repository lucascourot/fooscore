<?php

declare(strict_types=1);

namespace Fooscore\Tests\Integration\Gaming;

use Fooscore\Gaming\Adapters\ShowMatchDetails;
use Fooscore\Gaming\Match\MatchId;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group integration
 */
class ShowMatchDetailsTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var string
     */
    private $testMatchId = '6df9c8af-afeb-4422-ac60-5f271c738d76';

    /**
     * @var string
     */
    private $dir;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->dir = $kernel->getProjectDir().DIRECTORY_SEPARATOR.'var/';
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        @unlink($this->dir.$this->testMatchId.'.json');
    }

    public function testShouldShowMatchDetails(): void
    {
        // Given
        file_put_contents($this->dir.$this->testMatchId.'.json', <<<JSON
{
    "id": "6df9c8af-afeb-4422-ac60-5f271c738d76",
    "isWon": false,
    "score": {
        "blue": 0,
        "red": 0
    },
    "goals": [],
    "players": {
        "blue": {
            "back": {
                "id": "a",
                "name": "a"
            },
            "front": {
                "id": "b",
                "name": "b"
            }
        },
        "red": {
            "back": {
                "id": "c",
                "name": "c"
            },
            "front": {
                "id": "d",
                "name": "d"
            }
        }
    }
}
JSON
        );

        $showMatchDetails = new ShowMatchDetails($this->dir);

        // When
        $details = $showMatchDetails->showMatchDetails(new MatchId(Uuid::fromString($this->testMatchId)));

        // Then
        self::assertSame($details, [
            'id' => '6df9c8af-afeb-4422-ac60-5f271c738d76',
            'isWon' => false,
            'score' => [
                'blue' => 0,
                'red' => 0,
            ],
            'goals' => [],
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

    public function testCannotReadProjection(): void
    {
        $this->expectException(\RuntimeException::class);

        // Given
        $showMatchDetails = new ShowMatchDetails('unknowndir');

        // When
        $showMatchDetails->showMatchDetails(new MatchId(Uuid::fromString($this->testMatchId)));
    }
}
