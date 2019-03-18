<?php

declare(strict_types=1);

namespace Fooscore\Ranking\Infrastructure;

use Fooscore\Ranking\EloScores;
use Fooscore\Ranking\EloScoresRepository;
use Fooscore\Ranking\MatchResult;
use function file_get_contents;
use function file_put_contents;
use function json_decode;
use function json_encode;

final class JsonEloScoreRepository implements EloScoresRepository
{
    private const DEFAULT_ELO = 1000;
    /** @var string */
    private $rakingDir;

    public function __construct(string $rakingDir)
    {
        $this->rakingDir = $rakingDir;
    }

    public function get(MatchResult $matchResult) : EloScores
    {
        $player1 = $matchResult->winningTeam()->playerAId();
        $player2 = $matchResult->winningTeam()->playerBId();
        $player3 = $matchResult->losingTeam()->playerAId();
        $player4 = $matchResult->losingTeam()->playerBId();

        return new EloScores($matchResult, [
            $player1 => $this->getState()[$player1] ?? self::DEFAULT_ELO,
            $player2 => $this->getState()[$player2] ?? self::DEFAULT_ELO,
            $player3 => $this->getState()[$player3] ?? self::DEFAULT_ELO,
            $player4 => $this->getState()[$player4] ?? self::DEFAULT_ELO,
        ]);
    }

    public function save(EloScores $updatedEloScores) : void
    {
        $state = $this->getState();

        foreach ($updatedEloScores->playersWithScores() as $playerId => $scoreId) {
            $state[$playerId] = $scoreId;
        }

        file_put_contents($this->rakingDir . 'ranking.json', json_encode($state));
    }

    /**
     * @return mixed[]
     */
    private function getState() : array
    {
        $content = @file_get_contents($this->rakingDir . 'ranking.json');

        if ($content === false) {
            return [];
        }

        return json_decode($content, true) ?? [];
    }
}
