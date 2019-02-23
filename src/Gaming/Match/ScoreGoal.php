<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

use Fooscore\Gaming\CanScoreGoal;

final class ScoreGoal implements CanScoreGoal
{
    /**
     * @var MatchRepository
     */
    private $matchRepository;

    public function __construct(MatchRepository $matchRepository)
    {
        $this->matchRepository = $matchRepository;
    }

    public function scoreGoal(MatchId $matchId, Scorer $scorer): Match
    {
        $match = $this->matchRepository->get($matchId);

        $match->scoreGoal($scorer);

        $this->matchRepository->save($match);

        return $match;
    }
}
