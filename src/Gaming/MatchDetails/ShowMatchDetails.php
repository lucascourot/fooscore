<?php

declare(strict_types=1);

namespace Fooscore\Gaming\MatchDetails;

interface ShowMatchDetails
{
    /**
     * @return mixed[]
     */
    public function __invoke(string $matchId) : array;
}
