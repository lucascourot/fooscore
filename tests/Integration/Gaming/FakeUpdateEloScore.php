<?php

declare(strict_types=1);

namespace Fooscore\Tests\Integration\Gaming;

use Fooscore\Ranking\CanUpdateEloScore;
use Fooscore\Ranking\EloScores;
use Fooscore\Ranking\MatchResult;

final class FakeUpdateEloScore implements CanUpdateEloScore
{
    /**
     * @var EloScores
     */
    private $eloScoresToReturn;

    /**
     * @var MatchResult
     */
    private $matchResult;

    public function __construct(EloScores $eloScoresToReturn)
    {
        $this->eloScoresToReturn = $eloScoresToReturn;
    }

    public function updatePlayersScores(MatchResult $matchResult): EloScores
    {
        $this->matchResult = $matchResult;

        return $this->eloScoresToReturn;
    }

    public function getUsedMatchResult(): ?MatchResult
    {
        return $this->matchResult;
    }
}
