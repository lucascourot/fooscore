<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

use Fooscore\Gaming\CanStartMatch;

final class StartMatch implements CanStartMatch
{
    /**
     * @var MatchIdGenerator
     */
    private $matchIdGenerator;

    /**
     * @var MatchRepository
     */
    private $matchRepository;

    /**
     * @var Clock
     */
    private $clock;

    public function __construct(MatchIdGenerator $matchIdGenerator, MatchRepository $matchRepository, Clock $clock)
    {
        $this->matchIdGenerator = $matchIdGenerator;
        $this->matchRepository = $matchRepository;
        $this->clock = $clock;
    }

    public function startMatch(TeamBlue $teamBlue, TeamRed $teamRed): Match
    {
        $match = Match::start($this->matchIdGenerator->generate(), $teamBlue, $teamRed, $this->clock);

        $this->matchRepository->save($match);

        return $match;
    }
}
