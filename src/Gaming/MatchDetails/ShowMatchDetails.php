<?php

declare(strict_types=1);

namespace Fooscore\Gaming\MatchDetails;

use Fooscore\Gaming\CanShowMatchDetails;
use Fooscore\Gaming\Match\{
    Match, MatchId, MatchRepository
};

final class ShowMatchDetails implements CanShowMatchDetails
{
    /**
     * @var MatchRepository
     */
    private $matchRepository;

    public function __construct(MatchRepository $matchRepository)
    {
        $this->matchRepository = $matchRepository;
    }

    public function showMatchDetails(MatchId $matchId): MatchDetails
    {
        $match = $this->matchRepository->get($matchId);

        return MatchDetails::fromMatch($match);
    }
}
