<?php

declare(strict_types=1);

namespace Fooscore\Adapters\Gaming;

use Doctrine\DBAL\Connection;
use Fooscore\Gaming\Match\{
    DomainEvent, Match, MatchId, MatchRepository, VersionedEvent
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

    public function __construct(Connection $connection, DomainEventsFinder $domainEventsFinder)
    {
        $this->connection = $connection;
        $this->knownDomainEvents = $domainEventsFinder->getDomainEventsClassesIndexedByNames();
    }

    public function save(Match $match): void
    {
        $versionedEvents = $match->recordedEvents();

        foreach ($versionedEvents as $versionedEvent) {
            $domainEvent = $versionedEvent->domainEvent();

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
                'aggregate_version' => $versionedEvent->aggregateVersion(),
            ]);
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

        $versionedEvents = array_map(function (array $domainEventArray): VersionedEvent {
            foreach ($this->knownDomainEvents as $knownDomainEventName => $knownDomainEventClass) {
                if ($domainEventArray['event_name'] === $knownDomainEventName) {
                    /* @var DomainEvent $knownDomainEventClass */
                    $domainEvent = $knownDomainEventClass::fromEventDataArray(
                        json_decode($domainEventArray['event_data'], true)
                    );

                    return new VersionedEvent($domainEventArray['aggregate_version'], $domainEvent);
                }
            }

            throw new \RuntimeException(
                sprintf('Unknown domain event name : %s', $domainEventArray['event_name'])
            );
        }, $domainEventsArray);

        return Match::reconstituteFromHistory($versionedEvents);
    }
}
