<?php

declare(strict_types=1);

namespace Fooscore\Ranking;

class EloScore implements CanUpdateScore
{
    const COEFFICIENT = 20;
    const A = 400;
    const TEN = 10;
    const ROUND_PRECISION = 3;

    /**
     * @var PlayerRepository
     */
    private $playerRepository;

    public function __construct(PlayerRepository $playerRepository)
    {
        $this->playerRepository = $playerRepository;
    }

    public function updateScore(string $winningPlayerId1, string $winningPlayerId2, string $losingPlayerId1, string $losingPlayerId2): void
    {
        $winningPlayer1 = $this->playerRepository->get($winningPlayerId1);
        $winningPlayer2 = $this->playerRepository->get($winningPlayerId2);
        $losingPlayer1 = $this->playerRepository->get($losingPlayerId1);
        $losingPlayer2 = $this->playerRepository->get($losingPlayerId2);

        $winningTeamAverage = (int) (($winningPlayer1->score() + $winningPlayer2->score()) / 2);
        $losingTeamAverage = (int) (($losingPlayer1->score() + $losingPlayer2->score()) / 2);

        $winRating = $this->getWinRating($winningTeamAverage - $losingTeamAverage);

        $winningPlayer1->updateScore($winRating);
        $this->playerRepository->save($winningPlayer1);

        $winningPlayer2->updateScore($winRating);
        $this->playerRepository->save($winningPlayer2);

        $losingPlayer1->updateScore(-$winRating);
        $this->playerRepository->save($losingPlayer1);

        $losingPlayer2->updateScore(-$winRating);
        $this->playerRepository->save($losingPlayer2);
    }

    private function getWinRating(int $ratingDifference): int
    {
        $benefit = round(1 / (1 + \pow(self::TEN, (-$ratingDifference / self::A))), self::ROUND_PRECISION);

        return (int) round(self::COEFFICIENT * (1 - $benefit));
    }
}
