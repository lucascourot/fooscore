<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Gaming;

use Fooscore\Gaming\Match\Player;
use Fooscore\Gaming\Match\TeamBlue;
use Fooscore\Gaming\Match\TeamRed;

final class FakeTeam
{
    public static function blue(string $back, string $front) : TeamBlue
    {
        return new TeamBlue(
            new Player($back, $back),
            new Player($front, $front)
        );
    }

    public static function red(string $back, string $front) : TeamRed
    {
        return new TeamRed(
            new Player($back, $back),
            new Player($front, $front)
        );
    }
}
