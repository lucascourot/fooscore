<?php

declare(strict_types=1);

namespace Fooscore\Gaming\MatchDetails;

interface ShowMiddlefieldGoal
{
    /**
     * @return mixed[]
     *
     * @throws MiddlefieldGoalNotFound
     */
    public function __invoke(string $matchId, string $goalId) : array;
}
