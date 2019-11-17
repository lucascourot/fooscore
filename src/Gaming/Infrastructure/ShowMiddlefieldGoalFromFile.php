<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Infrastructure;

use Fooscore\Gaming\MatchDetails\MiddlefieldGoalNotFound;
use Fooscore\Gaming\MatchDetails\ShowMiddlefieldGoal;
use RuntimeException;
use function file_get_contents;
use function json_decode;
use function sprintf;
use function strval;

final class ShowMiddlefieldGoalFromFile implements ShowMiddlefieldGoal
{
    private const MIDDLEFIELD_TYPE = 'middlefield';

    /** @var string */
    private $dir;

    public function __construct(string $projectionDir)
    {
        $this->dir = $projectionDir;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(string $matchId, string $goalId) : array
    {
        $jsonDetails = @file_get_contents($this->dir . $matchId . '.json');

        if ($jsonDetails === false) {
            throw new RuntimeException('Cannot read projection file.');
        }

        $matchWithDetail = json_decode($jsonDetails, true);

        $askedGoal = null;
        foreach ($matchWithDetail['goals'] as $scoredGoal) {
            if (strval($scoredGoal['id']) !== $goalId || $scoredGoal['type'] !== self::MIDDLEFIELD_TYPE) {
                continue;
            }

            $askedGoal = $scoredGoal;
        }

        if ($askedGoal === null) {
            throw new MiddlefieldGoalNotFound(sprintf('Middlefield goal "%s" not found', $goalId));
        }

        return $askedGoal;
    }
}
