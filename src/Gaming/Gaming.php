<?php

declare(strict_types=1);

namespace Fooscore\Gaming;

use Fooscore\Gaming\Match\Match;
use Fooscore\Gaming\Match\MatchId;
use Fooscore\Gaming\Match\MatchIdGenerator;
use Fooscore\Gaming\Match\MatchRepository;
use Fooscore\Gaming\Match\Scorer;
use Fooscore\Gaming\Match\TeamBlue;
use Fooscore\Gaming\Match\TeamRed;

final class Gaming implements StartMatch, ScoreGoal
{
    /**
     * @var MatchIdGenerator
     */
    private $matchIdGenerator;

    /**
     * @var MatchRepository
     */
    private $matchRepository;

    public function __construct(MatchIdGenerator $matchIdGenerator, MatchRepository $matchRepository)
    {
        $this->matchIdGenerator = $matchIdGenerator;
        $this->matchRepository = $matchRepository;
    }

    public function startMatch(TeamBlue $teamBlue, TeamRed $teamRed): Match
    {
        $match = Match::start($this->matchIdGenerator->generate(), $teamBlue, $teamRed);

        $this->matchRepository->save($match);

        return $match;
    }

    public function scoreGoal(MatchId $matchId, Scorer $scorer): Match
    {
        $match = $this->matchRepository->get($matchId);

        $match->scoreGoal($scorer);

        $this->matchRepository->save($match);

        return $match;
    }
}
