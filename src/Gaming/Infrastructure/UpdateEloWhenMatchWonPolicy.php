<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Infrastructure;

use Fooscore\Gaming\Match\MatchRepository;
use Fooscore\Gaming\Match\MatchWasWon;
use Fooscore\Ranking\CanUpdateEloScore;
use Fooscore\Ranking\EloScores;
use Fooscore\Ranking\LosingTeam;
use Fooscore\Ranking\MatchResult;
use Fooscore\Ranking\WinningTeam;
use RuntimeException;

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

    public function on(MatchSymfonyEvent $event) : EloScores
    {
        $domainEvent = $event->domainEvent();

        if ($domainEvent instanceof MatchWasWon) {
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

        throw new RuntimeException('Expected event should be of type ' . MatchWasWon::class);
    }
}
