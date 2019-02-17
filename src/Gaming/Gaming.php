<?php

namespace Fooscore\Gaming;

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
