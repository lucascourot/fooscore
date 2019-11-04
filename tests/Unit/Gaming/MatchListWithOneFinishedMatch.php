<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Gaming;

use Fooscore\Gaming\Match\Clock;
use Fooscore\Gaming\Match\Goal;
use Fooscore\Gaming\Match\GoalWasScored;
use Fooscore\Gaming\Match\Match;
use Fooscore\Gaming\Match\MatchId;
use Fooscore\Gaming\Match\MatchRepository;
use Fooscore\Gaming\Match\MatchWasStarted;
use Fooscore\Gaming\Match\MatchWasWon;
use Fooscore\Gaming\Match\ScoredAt;
use Fooscore\Gaming\Match\Scorer;
use Fooscore\Gaming\Match\VersionedEvent;
use Ramsey\Uuid\Uuid;

final class MatchListWithOneFinishedMatch implements MatchRepository
{
    private const MATCH_ID = '6df9c8af-afeb-4422-ac60-5f271c738d76';

    /** @var array<string,Match> */
    private $matches;

    public function __construct(Clock $clock)
    {
        $teamBlue = FakeTeam::blue('a', 'b');
        $teamRed = FakeTeam::red('c', 'd');
        $scorer = Scorer::fromTeamAndPosition('blue', 'back');

        $this->matches = [
            self::MATCH_ID =>
                Match::reconstituteFromHistory([
                    new VersionedEvent(1, new MatchWasStarted($this->matchId(), $teamBlue, $teamRed, $clock->now())),
                    new VersionedEvent(2, new GoalWasScored(new Goal(1, $scorer, new ScoredAt(0)))),
                    new VersionedEvent(3, new GoalWasScored(new Goal(2, $scorer, new ScoredAt(0)))),
                    new VersionedEvent(4, new GoalWasScored(new Goal(3, $scorer, new ScoredAt(0)))),
                    new VersionedEvent(5, new GoalWasScored(new Goal(4, $scorer, new ScoredAt(0)))),
                    new VersionedEvent(6, new GoalWasScored(new Goal(5, $scorer, new ScoredAt(0)))),
                    new VersionedEvent(7, new GoalWasScored(new Goal(6, $scorer, new ScoredAt(0)))),
                    new VersionedEvent(8, new GoalWasScored(new Goal(7, $scorer, new ScoredAt(0)))),
                    new VersionedEvent(9, new GoalWasScored(new Goal(8, $scorer, new ScoredAt(0)))),
                    new VersionedEvent(10, new GoalWasScored(new Goal(9, $scorer, new ScoredAt(0)))),
                    new VersionedEvent(11, new GoalWasScored(new Goal(10, $scorer, new ScoredAt(0)))),
                    new VersionedEvent(12, new MatchWasWon('blue')),
                ]),
        ];
    }

    public function save(Match $match) : void
    {
    }

    public function get(MatchId $matchId) : Match
    {
        return $this->matches[self::MATCH_ID];
    }

    public function matchId() : MatchId
    {
        return new MatchId(Uuid::fromString(self::MATCH_ID));
    }
}
