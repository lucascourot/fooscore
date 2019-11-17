<?php

declare(strict_types=1);

namespace Fooscore\Tests\Integration\Gaming;

use Fooscore\Gaming\Infrastructure\ShowMiddlefieldGoalFromFile;
use Fooscore\Gaming\MatchDetails\MiddlefieldGoalNotFound;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @group integration
 */
class ShowMiddlefieldGoalFromFileTest extends TestCase
{
    /** @var string */
    private $testMatchId = '6df9c8af-afeb-4422-ac60-5f271c738d76';

    /** @var string */
    private $dir;

    protected function setUp() : void
    {
        $this->dir = __DIR__ . '/match-view/';
    }

    public function testShouldShowMiddlefieldGoalFromFile() : void
    {
        // Given
        $showMiddlefieldGoal = new ShowMiddlefieldGoalFromFile($this->dir);
        $goalNumberToShow = '2';

        // When
        $details = $showMiddlefieldGoal($this->testMatchId, $goalNumberToShow);

        // Then
        self::assertSame($details, [
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
        ]);
    }

    public function testShouldNotShowRegularGoal() : void
    {
        // Then
        $this->expectException(MiddlefieldGoalNotFound::class);

        // Given
        $showMiddlefieldGoal = new ShowMiddlefieldGoalFromFile($this->dir);
        $goalNumberToShow = '1';

        // When
        $showMiddlefieldGoal($this->testMatchId, $goalNumberToShow);
    }

    public function testCannotReadProjection() : void
    {
        $this->expectException(RuntimeException::class);

        // Given
        $showMiddlefieldGoal = new ShowMiddlefieldGoalFromFile('unknowndir');

        // When
        $showMiddlefieldGoal($this->testMatchId, '1');
    }
}
