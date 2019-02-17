<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

use Fooscore\Gaming\StartMatch as StartMatchInputPort;

final class StartMatch implements StartMatchInputPort
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
}
