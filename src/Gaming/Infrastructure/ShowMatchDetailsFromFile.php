<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Infrastructure;

use Fooscore\Gaming\MatchDetails\ShowMatchDetails;
use RuntimeException;
use function file_get_contents;
use function json_decode;

final class ShowMatchDetailsFromFile implements ShowMatchDetails
{
    /** @var string */
    private $dir;

    public function __construct(string $projectionDir)
    {
        $this->dir = $projectionDir;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(string $matchId) : array
    {
        $details = @file_get_contents($this->dir . $matchId . '.json');

        if ($details === false) {
            throw new RuntimeException('Cannot read projection file.');
        }

        return json_decode($details, true);
    }
}
