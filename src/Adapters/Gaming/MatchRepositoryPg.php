<?php

namespace Fooscore\Adapters\Gaming;

use Fooscore\Gaming\Match;
use Fooscore\Gaming\MatchId;
use Fooscore\Gaming\MatchRepository;
use Fooscore\Gaming\TeamBlue;
use Fooscore\Gaming\TeamRed;
use Ramsey\Uuid\Uuid;

final class MatchRepositoryPg implements MatchRepository
{
    public function save(Match $match): void
    {
//        $domainEvents = $match->popRecordedEvents();
//
//        foreach ($domainEvents as $domainEvent) {
//            try {
//                $this->connection->persist($domainEvent);
//                $this->eventDispatcher->dispatch($domainEvent);
//            } catch (\Throwable $exception) {
//                $this->logger->error($exception->getMessage());
//            }
//        }
    }

    public function get(MatchId $matchId): Match
    {
        $matchId = new MatchId(Uuid::fromString('6df9c8af-afeb-4422-ac60-5f271c738d76'));

        return Match::start($matchId, new TeamBlue('a', 'b'), new TeamRed('c', 'd'));
    }
}
