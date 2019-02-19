<?php

declare(strict_types=1);

namespace Fooscore\Adapters\Gaming;

use Doctrine\DBAL\Connection;
use Fooscore\Gaming\Match\{
    DomainEvent, GoalWasScored, Match, MatchId, MatchRepository, MatchWasStarted
};
use Ramsey\Uuid\Uuid;

final class MatchRepositoryPg implements MatchRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var array
     */
    private $knownDomainEvents;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->knownDomainEvents = [
            MatchWasStarted::eventName() => MatchWasStarted::class,
            GoalWasScored::eventName() => GoalWasScored::class,
        ];
    }

    public function save(Match $match): void
    {
        $domainEvents = $match->recordedEvents();

        $aggregateVersion = 1;
        foreach ($domainEvents as $domainEvent) {
//            try {
            $statement = $this->connection->prepare(<<<SQL
                INSERT INTO event_store
                    (event_id, event_name, event_data, aggregate_id, aggregate_type, aggregate_version, created_at)
                VALUES (:event_id, :event_name, :event_data, :aggregate_id, :aggregate_type, :aggregate_version, NOW());
SQL
);
            $statement->execute([
                    'event_id' => Uuid::uuid1(),
                    'event_name' => $domainEvent::eventName(),
                    'event_data' => json_encode($domainEvent->eventDataAsArray()),
                    'aggregate_id' => $match->id()->value()->toString(),
                    'aggregate_type' => 'match',
                    'aggregate_version' => $aggregateVersion++,
                ]);
//                $this->eventDispatcher->dispatch($domainEvent);
//            } catch (\Throwable $exception) {
//                $this->logger->error($exception->getMessage());
//            }
        }
    }

    public function get(MatchId $matchId): Match
    {
        $statement = $this->connection->prepare(<<<SQL
                SELECT
                    *
                FROM
                    event_store
                WHERE
                    aggregate_id = :aggregate_id
                    AND aggregate_type = :aggregate_type
                ORDER BY event_store.event_id;
SQL
        );
        $statement->execute([
            'aggregate_id' => $matchId->value()->toString(),
            'aggregate_type' => 'match',
        ]);

        $domainEventsArray = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $domainEvents = array_map(function (array $domainEventArray): DomainEvent {
            foreach ($this->knownDomainEvents as $knownDomainEventName => $knownDomainEventClass) {
                if ($domainEventArray['event_name'] === $knownDomainEventName) {
                    /* @var DomainEvent $knownDomainEventClass */
                    return $knownDomainEventClass::fromEventDataArray(
                        json_decode($domainEventArray['event_data'], true)
                    );
                }
            }

            throw new \RuntimeException(
                sprintf('Unknown domain event name : %s', $domainEventArray['event_name'])
            );
        }, $domainEventsArray);

        return Match::reconstituteFromHistory($domainEvents);
    }
}
