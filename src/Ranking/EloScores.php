<?php

declare(strict_types=1);

namespace Fooscore\Ranking;

use InvalidArgumentException;
use function array_key_exists;
use function max;
use function pow;
use function round;
use function sprintf;

final class EloScores
{
    private const RATING_COEFFICIENT = 50;
    private const RATING_INTERVAL_SCALE_WEIGHT_FACTOR = 1000;
    private const BASE_TEN = 10;
    private const MATCH_WON = 1;
    private const MIN_SCORE = 0;

    /** @var MatchResult */
    private $matchResult;

    /** @var int[] */
    private $playersWithScores;

    /**
     * @param int[] $playersWithScores
     */
    public function __construct(MatchResult $matchResult, array $playersWithScores)
    {
        $this->matchResult = $matchResult;
        $this->playersWithScores = $playersWithScores;
    }

    public function getScoreForPlayerId(string $playerId) : int
    {
        if (array_key_exists($playerId, $this->playersWithScores) === false) {
            throw new InvalidArgumentException(sprintf('Player with id "%s" does not exist.', $playerId));
        }

        return $this->playersWithScores[$playerId];
    }

    public function recalculate() : void
    {
        $ratingDifference = AverageEloScore::team(
            $this->getScoreForPlayerId($this->matchResult->winningTeam()->playerAId()),
            $this->getScoreForPlayerId($this->matchResult->winningTeam()->playerBId())
        ) - AverageEloScore::team(
            $this->getScoreForPlayerId($this->matchResult->losingTeam()->playerAId()),
            $this->getScoreForPlayerId($this->matchResult->losingTeam()->playerBId())
        );

        $earnedRatingPoints = $this->earnedRatingPoints($ratingDifference);

        $this->playersWithScores[$this->matchResult->winningTeam()->playerAId()] += $earnedRatingPoints;
        $this->playersWithScores[$this->matchResult->winningTeam()->playerBId()] += $earnedRatingPoints;
        $this->playersWithScores[$this->matchResult->losingTeam()->playerAId()] -= $earnedRatingPoints;
        $this->playersWithScores[$this->matchResult->losingTeam()->playerBId()] -= $earnedRatingPoints;

        $this->ensureScoresCannotBeNegative();
    }

    /**
     * @return int[]
     */
    public function playersWithScores() : array
    {
        return $this->playersWithScores;
    }

    private function earnedRatingPoints(int $ratingDifference) : int
    {
        $winExpectancyPercentage = 1 / (1 + pow(
            self::BASE_TEN,
            (-$ratingDifference / self::RATING_INTERVAL_SCALE_WEIGHT_FACTOR)
        ));

        return (int) round(self::RATING_COEFFICIENT * (self::MATCH_WON - $winExpectancyPercentage));
    }

    private function ensureScoresCannotBeNegative() : void
    {
        $this->playersWithScores[$this->matchResult->losingTeam()->playerAId()] = max(
            $this->playersWithScores[$this->matchResult->losingTeam()->playerAId()],
            self::MIN_SCORE
        );

        $this->playersWithScores[$this->matchResult->losingTeam()->playerBId()] = max(
            $this->playersWithScores[$this->matchResult->losingTeam()->playerBId()],
            self::MIN_SCORE
        );
    }
}
