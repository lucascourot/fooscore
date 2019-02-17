<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

use Fooscore\Gaming\ScoreGoal as ScoreGoalInputPort;

final class UseCaseScoreGoal implements ScoreGoalInputPort
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
