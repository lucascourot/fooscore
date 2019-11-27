<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Infrastructure;

use Doctrine\DBAL\Connection;
use Fooscore\Gaming\Infrastructure\Events\PublishedEventFactory;
use Fooscore\Gaming\Match\DomainEvent;
use Fooscore\Gaming\Match\Match;
use Fooscore\Gaming\Match\MatchId;
use Fooscore\Gaming\Match\MatchRepository;
use Fooscore\Gaming\Match\VersionedEvent;
use InvalidArgumentException;
use PDO;
use Ramsey\Uuid\Uuid;
use RuntimeException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use function array_map;
use function count;
use function json_decode;
use function json_encode;
use function sprintf;

final class MatchRepositoryPg implements MatchRepository
{
    public const FETCH_EVENTS_QUERY = <<<SQL
        SELECT
            *
        FROM
            event_store
        WHERE
            aggregate_id = :aggregate_id
            AND aggregate_type = :aggregate_type
        ORDER BY event_store.aggregate_version ASC;
SQL;

    /** @var Connection */
    private $connection;

    /** @var string[]|DomainEvent[] */
    private $knownDomainEvents;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        Connection $connection,
        DomainEventsFinder $domainEventsFinder,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->connection = $connection;
        $this->knownDomainEvents = $domainEventsFinder->getDomainEventsClassesIndexedByNames();
        $this->eventDispatcher = $eventDispatcher;
    }

    public function save(Match $match) : void
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
                'event_id' => Uuid::uuid4(),
                'event_name' => $domainEvent::eventName(),
                'event_data' => json_encode($domainEvent->eventDataAsArray()),
                'aggregate_id' => $match->id()->value()->toString(),
                'aggregate_type' => 'match',
                'aggregate_version' => $versionedEvent->aggregateVersion(),
            ]);

            $publishedEvent = PublishedEventFactory::create($match->id(), $domainEvent);

            $this->eventDispatcher->dispatch($publishedEvent);
        }
    }

    public function get(MatchId $matchId) : Match
    {
        $statement = $this->connection->prepare(self::FETCH_EVENTS_QUERY);
        $statement->execute([
            'aggregate_id' => $matchId->value()->toString(),
            'aggregate_type' => 'match',
        ]);

        $domainEventsArray = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (count($domainEventsArray) === 0) {
            throw new InvalidArgumentException('Match not found.');
        }

        $versionedEvents = array_map(function (array $domainEventArray) : VersionedEvent {
            foreach ($this->knownDomainEvents as $knownDomainEventName => $knownDomainEventClass) {
                if ($domainEventArray['event_name'] === $knownDomainEventName) {
                    /** @var DomainEvent $knownDomainEventClass */
                    $domainEvent = $knownDomainEventClass::fromEventDataArray(
                        json_decode($domainEventArray['event_data'], true)
                    );

                    return new VersionedEvent($domainEventArray['aggregate_version'], $domainEvent);
                }
            }

            throw new RuntimeException(
                sprintf('Unknown domain event name : %s', $domainEventArray['event_name'])
            );
        }, $domainEventsArray);

        return Match::reconstituteFromHistory($versionedEvents);
    }
}
