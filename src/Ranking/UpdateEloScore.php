<?php

declare(strict_types=1);

namespace Fooscore\Ranking;

final class UpdateEloScore implements CanUpdateEloScore
{
    /**
     * @var EloScoresRepository
     */
    private $playerRepository;

    public function __construct(EloScoresRepository $playerRepository)
    {
        $this->playerRepository = $playerRepository;
    }

    public function updatePlayersScores(MatchResult $matchResult): EloScores
    {
        $eloScoresForMatch = $this->playerRepository->get($matchResult);

        $eloScoresForMatch->recalculate();

        $this->playerRepository->save($eloScoresForMatch);

        return $eloScoresForMatch;
    }
}
