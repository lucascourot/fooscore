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
    /**
     * @var MatchRepository
     */
    private $matchRepository;

    /**
     * @var CanUpdateEloScore
     */
    private $canUpdateEloScore;

    public function __construct(MatchRepository $matchRepository, CanUpdateEloScore $canUpdateEloScore)
    {
        $this->matchRepository = $matchRepository;
        $this->canUpdateEloScore = $canUpdateEloScore;
    }

    public function on(MatchSymfonyEvent $event): EloScores
    {
        $domainEvent = $event->domainEvent();

        if ($domainEvent instanceof MatchWasWon) {
            $match = $this->matchRepository->get($event->matchId());

            if ($domainEvent->teamWinner() === 'blue') {
                $matchResult = new MatchResult(
                    new WinningTeam(
                        $match->getTeamBlue()->back()->id(),
                        $match->getTeamBlue()->front()->id()
                    ),
                    new LosingTeam(
                        $match->getTeamRed()->back()->id(),
                        $match->getTeamRed()->front()->id()
                    )
                );
            } else {
                $matchResult = new MatchResult(
                    new WinningTeam(
                        $match->getTeamRed()->back()->id(),
                        $match->getTeamRed()->front()->id()
                    ),
                    new LosingTeam(
                        $match->getTeamBlue()->back()->id(),
                        $match->getTeamBlue()->front()->id()
                    )
                );
            }

            return $this->canUpdateEloScore->updatePlayersScores($matchResult);
        }

        throw new RuntimeException('Expected event should be of type '.MatchWasWon::class);
    }
}
