<?php

declare(strict_types=1);

namespace Fooscore\Tests\Integration\Gaming;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Fooscore\Adapters\Gaming\DomainEventsFinder;
use Fooscore\Adapters\Gaming\MatchRepositoryPg;
use Fooscore\Adapters\Gaming\SystemClock;
use Fooscore\Gaming\Match\{
    Match, MatchId, Scorer, TeamBlue, TeamRed
};
use Fooscore\Gaming\MatchDetails\MatchDetails;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group integration
 */
class MatchRepositoryPgTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $testMatchId = '6df9c8af-afeb-4422-ac60-5f271c738d76';

    /**
     * @var DomainEventsFinder
     */
    private $domainEventsFinder;

    public function testShouldReadFromPgsql(): void
    {
        // Given
        $adapter = new MatchRepositoryPg($this->connection, $this->domainEventsFinder);

        $teamBlue = new TeamBlue('a', 'b');
        $teamRed = new TeamRed('c', 'd');
        $matchId = new MatchId(Uuid::fromString($this->testMatchId));

        $clock = new SystemClock();

        $match = Match::start($matchId, $teamBlue, $teamRed, $clock);
        $match->scoreGoal(Scorer::fromTeamAndPosition('blue', 'back'), $clock);
        $adapter->save($match);

        // When
        $reconstitutedMatch = $adapter->get($matchId);

        // Then
        self::assertEquals(MatchDetails::fromMatch($match)->details(), MatchDetails::fromMatch($reconstitutedMatch)->details());
    }

    public function testShouldPersistSameEventsTwiceWithDifferentVersionsOfAggregate(): void
    {
        // Given
        $adapter = new MatchRepositoryPg($this->connection, $this->domainEventsFinder);

        $teamBlue = new TeamBlue('a', 'b');
        $teamRed = new TeamRed('c', 'd');
        $matchId = new MatchId(Uuid::fromString($this->testMatchId));

        $clock = new SystemClock();

        // When
        $match = Match::start($matchId, $teamBlue, $teamRed, $clock);
        $match->scoreGoal(Scorer::fromTeamAndPosition('blue', 'back'), $clock);
        $match->scoreGoal(Scorer::fromTeamAndPosition('blue', 'back'), $clock);
        $adapter->save($match);

        // Then
        $domainEventsArray = $this->fetchDomainEventsForAggregate($matchId);

        self::assertSame([
            'match_was_started',
            'goal_was_scored',
            'goal_was_scored',
        ], array_column($domainEventsArray, 'event_name'));

        self::assertSame([1, 2, 3], array_column($domainEventsArray, 'aggregate_version'));
    }

    public function testShouldAddNewEventsAfterBeingFetched(): void
    {
        // Given
        $adapter = new MatchRepositoryPg($this->connection, $this->domainEventsFinder);

        $teamBlue = new TeamBlue('a', 'b');
        $teamRed = new TeamRed('c', 'd');
        $matchId = new MatchId(Uuid::fromString($this->testMatchId));

        $clock = new SystemClock();

        $match = Match::start($matchId, $teamBlue, $teamRed, $clock);
        $adapter->save($match);

        // When
        $reconstitutedMatch = $adapter->get($matchId);
        $reconstitutedMatch->scoreGoal(Scorer::fromTeamAndPosition('blue', 'back'), $clock);
        $adapter->save($reconstitutedMatch);

        // Then
        $domainEventsArray = $this->fetchDomainEventsForAggregate($matchId);

        self::assertSame([
            'match_was_started',
            'goal_was_scored',
        ], array_column($domainEventsArray, 'event_name'));

        self::assertSame([1, 2], array_column($domainEventsArray, 'aggregate_version'));
    }

    public function testShouldPersistAndReadMatchWasWon(): void
    {
        // Given
        $adapter = new MatchRepositoryPg($this->connection, $this->domainEventsFinder);

        $teamBlue = new TeamBlue('a', 'b');
        $teamRed = new TeamRed('c', 'd');
        $matchId = new MatchId(Uuid::fromString($this->testMatchId));

        $clock = new SystemClock();

        $match = Match::start($matchId, $teamBlue, $teamRed, $clock);
        $match->scoreGoal(Scorer::fromTeamAndPosition('blue', 'back'), $clock);
        $match->scoreGoal(Scorer::fromTeamAndPosition('blue', 'back'), $clock);
        $match->scoreGoal(Scorer::fromTeamAndPosition('blue', 'back'), $clock);
        $match->scoreGoal(Scorer::fromTeamAndPosition('blue', 'back'), $clock);
        $match->scoreGoal(Scorer::fromTeamAndPosition('blue', 'back'), $clock);
        $match->scoreGoal(Scorer::fromTeamAndPosition('blue', 'back'), $clock);
        $match->scoreGoal(Scorer::fromTeamAndPosition('blue', 'back'), $clock);
        $match->scoreGoal(Scorer::fromTeamAndPosition('blue', 'back'), $clock);
        $match->scoreGoal(Scorer::fromTeamAndPosition('blue', 'back'), $clock);
        $match->scoreGoal(Scorer::fromTeamAndPosition('blue', 'back'), $clock);

        // When
        $adapter->save($match);
        $reconstitutedMatch = $adapter->get($matchId);

        // Then
        $domainEventsArray = $this->fetchDomainEventsForAggregate($matchId);

        self::assertInstanceOf(Match::class, $reconstitutedMatch);

        self::assertSame([
            'match_was_started',
            'goal_was_scored',
            'goal_was_scored',
            'goal_was_scored',
            'goal_was_scored',
            'goal_was_scored',
            'goal_was_scored',
            'goal_was_scored',
            'goal_was_scored',
            'goal_was_scored',
            'goal_was_scored',
            'match_was_won',
        ], array_column($domainEventsArray, 'event_name'));

        self::assertSame(end($domainEventsArray)['event_data'], '{"teamWinner": "blue"}');
    }

    public function testShouldAvoidRaceConditions(): void
    {
        // Given
        $adapter = new MatchRepositoryPg($this->connection, $this->domainEventsFinder);

        $teamBlue = new TeamBlue('a', 'b');
        $teamRed = new TeamRed('c', 'd');
        $matchId = new MatchId(Uuid::fromString($this->testMatchId));

        $clock = new SystemClock();

        $match = Match::start($matchId, $teamBlue, $teamRed, $clock);
        $adapter->save($match);

        // When
        $firstFetchedAggregate = $adapter->get($matchId);
        $firstFetchedAggregate->scoreGoal(Scorer::fromTeamAndPosition('blue', 'back'), $clock);

        $raceConditionAggregate = $adapter->get($matchId);
        $raceConditionAggregate->scoreGoal(Scorer::fromTeamAndPosition('red', 'front'), $clock);
        $raceConditionAggregate->scoreGoal(Scorer::fromTeamAndPosition('red', 'back'), $clock);

        $adapter->save($firstFetchedAggregate);

        $thrownException = null;
        try {
            $adapter->save($raceConditionAggregate);
        } catch (\Exception $exception) {
            $thrownException = $exception;
        }

        // Then
        $domainEventsArray = $this->fetchDomainEventsForAggregate($matchId);

        self::assertSame([
            'match_was_started',
            'goal_was_scored',
        ], array_column($domainEventsArray, 'event_name'));

        self::assertSame([1, 2], array_column($domainEventsArray, 'aggregate_version'));

        $secondEventData = json_decode($domainEventsArray[1]['event_data'], true);
        self::assertSame('blue', $secondEventData['team']);
        self::assertSame('back', $secondEventData['position']);
        self::assertInstanceOf(UniqueConstraintViolationException::class, $thrownException);
    }

    public function testShouldThrowExceptionIfMatchNotFound(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        // Given
        $adapter = new MatchRepositoryPg($this->connection, $this->domainEventsFinder);

        $matchId = new MatchId(Uuid::fromString($this->testMatchId));

        // When
        $adapter->get($matchId);
    }

    public function testShouldThrowExceptionIfUnknownEvent(): void
    {
        $this->expectException(\RuntimeException::class);

        // Given
        $matchId = new MatchId(Uuid::fromString($this->testMatchId));
        $statement = $this->connection->prepare(<<<SQL
                INSERT INTO event_store
                    (event_id, event_name, event_data, aggregate_id, aggregate_type, aggregate_version, created_at)
                VALUES (:event_id, :event_name, :event_data, :aggregate_id, :aggregate_type, :aggregate_version, NOW());
SQL
        );
        $statement->execute([
            'event_id' => Uuid::uuid1(),
            'event_name' => 'ERROR',
            'event_data' => json_encode([]),
            'aggregate_id' => $matchId->value()->toString(),
            'aggregate_type' => 'match',
            'aggregate_version' => 1,
        ]);
        $adapter = new MatchRepositoryPg($this->connection, $this->domainEventsFinder);

        // When
        $adapter->get($matchId);
    }

    protected function setUp(): void
    {
        self::bootKernel();

        /** @var Connection $connection */
        $connection = self::$container->get(Connection::class);
        $this->connection = $connection;

        /** @var DomainEventsFinder $domainEventsFinder */
        $domainEventsFinder = self::$container->get(DomainEventsFinder::class);
        $this->domainEventsFinder = $domainEventsFinder;

        $this->deleteTestAggregateEvents();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->deleteTestAggregateEvents();
    }

    private function deleteTestAggregateEvents(): void
    {
        $this->connection->exec(
            "DELETE FROM event_store WHERE aggregate_id = '{$this->testMatchId}'"
        );
    }

    private function fetchDomainEventsForAggregate(MatchId $matchId): array
    {
        $statement = $this->connection->prepare(MatchRepositoryPg::FETCH_EVENTS_QUERY);
        $statement->execute([
            'aggregate_id' => $matchId->value()->toString(),
            'aggregate_type' => 'match',
        ]);

        $domainEventsArray = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $domainEventsArray;
    }
}
