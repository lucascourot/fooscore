<?php

namespace Fooscore\Gaming;

final class Gaming implements StartMatch
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

    public function startMatch(TeamBlue $teamBlue, TeamRed $teamRed): MatchId
    {
        $match = Match::start($this->matchIdGenerator->generate(), $teamBlue, $teamRed);

        $this->matchRepository->save($match);

        return $match->id();
    }
}
