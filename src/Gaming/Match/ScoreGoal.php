<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

use Fooscore\Gaming\CanScoreGoal;

final class ScoreGoal implements CanScoreGoal
{
    /** @var MatchRepository */
    private $matchRepository;

    /** @var Clock */
    private $clock;

    public function __construct(MatchRepository $matchRepository, Clock $clock)
    {
        $this->matchRepository = $matchRepository;
        $this->clock = $clock;
    }

    public function scoreGoal(MatchId $matchId, Scorer $scorer) : Match
    {
        $match = $this->matchRepository->get($matchId);

        $match->scoreGoal($scorer, $this->clock);

        $this->matchRepository->save($match);

        return $match;
    }
}
