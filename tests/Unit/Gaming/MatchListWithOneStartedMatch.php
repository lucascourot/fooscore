<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Gaming;

use Fooscore\Gaming\Match\Clock;
use Fooscore\Gaming\Match\Match;
use Fooscore\Gaming\Match\MatchId;
use Fooscore\Gaming\Match\MatchRepository;
use Fooscore\Gaming\Match\MatchWasStarted;
use Fooscore\Gaming\Match\VersionedEvent;
use Ramsey\Uuid\Uuid;

final class MatchListWithOneStartedMatch implements MatchRepository
{
    private const MATCH_ID = '6df9c8af-afeb-4422-ac60-5f271c738d76';

    /** @var array<string,Match> */
    private $matches;

    /** @var Match */
    private $savedMatch;

    public function __construct(Clock $clock)
    {
        $this->matches = [
            self::MATCH_ID => Match::reconstituteFromHistory([
                new VersionedEvent(1, new MatchWasStarted(
                    $this->matchId(),
                    FakeTeam::blue('a', 'b'),
                    FakeTeam::red('c', 'd'),
                    $clock->now()
                )),
            ]),
        ];
    }

    public function save(Match $match) : void
    {
        $this->savedMatch = $match;
    }

    public function get(MatchId $matchId) : Match
    {
        return $this->matches[self::MATCH_ID];
    }

    public function matchId() : MatchId
    {
        return new MatchId(Uuid::fromString(self::MATCH_ID));
    }

    public function savedMatch() : Match
    {
        return $this->savedMatch;
    }
}
