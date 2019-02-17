<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Gaming;

use Fooscore\Gaming\Match\Scorer;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 *
 * Player in match who just scored the goal
 */
class ScorerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testShouldBeBlueBack(): void
    {
        $scorer = Scorer::fromTeamAndPosition('blue', 'back');

        self::assertSame('blue', $scorer->team());
        self::assertSame('back', $scorer->position());
    }

    public function testShouldBeBlueFront(): void
    {
        $scorer = Scorer::fromTeamAndPosition('blue', 'front');

        self::assertSame('blue', $scorer->team());
        self::assertSame('front', $scorer->position());
    }

    public function testShouldBeRedBack(): void
    {
        $scorer = Scorer::fromTeamAndPosition('red', 'back');

        self::assertSame('red', $scorer->team());
        self::assertSame('back', $scorer->position());
    }

    public function testShouldBeRedFront(): void
    {
        $scorer = Scorer::fromTeamAndPosition('red', 'front');

        self::assertSame('red', $scorer->team());
        self::assertSame('front', $scorer->position());
    }

    public function testShouldOnlyBelongToTeamBlueOrRed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Scorer::fromTeamAndPosition('yellow', 'front');
    }

    public function testShouldOnlyBelongToPositionBackOrFront(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Scorer::fromTeamAndPosition('blue', 'middle');
    }

    public function testShouldConvertToLowercase(): void
    {
        $scorer = Scorer::fromTeamAndPosition('rEd', 'froNt');

        self::assertSame('red', $scorer->team());
        self::assertSame('front', $scorer->position());
    }
}
