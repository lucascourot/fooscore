<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Match;

use Fooscore\Gaming\ShowMatchDetails as ShowMatchDetailsInputPort;

final class ShowMatchDetails implements ShowMatchDetailsInputPort
{
    /**
     * @var MatchRepository
     */
    private $matchRepository;

    public function __construct(MatchRepository $matchRepository)
    {
        $this->matchRepository = $matchRepository;
    }

    public function showMatchDetails(MatchId $matchId): Match
    {
        $match = $this->matchRepository->get($matchId);

        return $match;
    }
}
