<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Infrastructure;

use Fooscore\Gaming\Infrastructure\Events\MatchWasWonPublishedEvent;
use Fooscore\Gaming\Match\MatchRepository;
use Fooscore\Ranking\CanUpdateEloScore;
use Fooscore\Ranking\EloScores;
use Fooscore\Ranking\LosingTeam;
use Fooscore\Ranking\MatchResult;
use Fooscore\Ranking\WinningTeam;

final class UpdateEloWhenMatchWonPolicy
{
    /** @var MatchRepository */
    private $matchRepository;

    /** @var CanUpdateEloScore */
    private $canUpdateEloScore;

    public function __construct(MatchRepository $matchRepository, CanUpdateEloScore $canUpdateEloScore)
    {
        $this->matchRepository = $matchRepository;
        $this->canUpdateEloScore = $canUpdateEloScore;
    }

    public function onMatchWasWon(MatchWasWonPublishedEvent $event) : EloScores
    {
        $domainEvent = $event->domainEvent();

        $match = $this->matchRepository->get($event->matchId());

        if ($domainEvent->teamWinner() === 'blue') {
            $matchResult = new MatchResult(
                new WinningTeam(
                    $match->teamBlue()->back()->id(),
                    $match->teamBlue()->front()->id()
                ),
                new LosingTeam(
                    $match->teamRed()->back()->id(),
                    $match->teamRed()->front()->id()
                )
            );
        } else {
            $matchResult = new MatchResult(
                new WinningTeam(
                    $match->teamRed()->back()->id(),
                    $match->teamRed()->front()->id()
                ),
                new LosingTeam(
                    $match->teamBlue()->back()->id(),
                    $match->teamBlue()->front()->id()
                )
            );
        }

        return $this->canUpdateEloScore->updatePlayersScores($matchResult);
    }
}
