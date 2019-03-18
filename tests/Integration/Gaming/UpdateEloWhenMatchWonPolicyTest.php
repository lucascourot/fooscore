<?php

declare(strict_types=1);

namespace Fooscore\Tests\Integration\Gaming;

use DateTimeImmutable;
use Fooscore\Gaming\Infrastructure\MatchSymfonyEvent;
use Fooscore\Gaming\Infrastructure\UpdateEloWhenMatchWonPolicy;
use Fooscore\Gaming\Match\Goal;
use Fooscore\Gaming\Match\GoalWasScored;
use Fooscore\Gaming\Match\Match;
use Fooscore\Gaming\Match\MatchId;
use Fooscore\Gaming\Match\MatchRepository;
use Fooscore\Gaming\Match\MatchWasStarted;
use Fooscore\Gaming\Match\MatchWasWon;
use Fooscore\Gaming\Match\ScoredAt;
use Fooscore\Gaming\Match\Scorer;
use Fooscore\Gaming\Match\VersionedEvent;
use Fooscore\Ranking\EloScores;
use Fooscore\Ranking\LosingTeam;
use Fooscore\Ranking\MatchResult;
use Fooscore\Ranking\WinningTeam;
use Fooscore\Tests\Unit\Gaming\FakeTeam;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use RuntimeException;

/**
 * @group integration
 */
class UpdateEloWhenMatchWonPolicyTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testShouldUpdateScoreWhenBlueTeamWins() : void
    {
        // Given
        $matchId = new MatchId(Uuid::fromString('6df9c8af-afeb-4422-ac60-5f271c738d76'));
        $scorer = Scorer::fromTeamAndPosition('blue', 'back');

        $teamBlue = FakeTeam::blue('a', 'b');
        $teamRed = FakeTeam::red('c', 'd');

        $matchRepository = Mockery::mock(MatchRepository::class);
        $matchRepository->allows('get')->with($matchId)->andReturns(
            Match::reconstituteFromHistory([
                new VersionedEvent(1, new MatchWasStarted(
                    $matchId,
                    $teamBlue,
                    $teamRed,
                    new DateTimeImmutable('2000-01-01 00:00:00')
                )),
                new VersionedEvent(2, new GoalWasScored(new Goal(1, $scorer, new ScoredAt(0)))),
                new VersionedEvent(3, new GoalWasScored(new Goal(2, $scorer, new ScoredAt(0)))),
                new VersionedEvent(4, new GoalWasScored(new Goal(3, $scorer, new ScoredAt(0)))),
                new VersionedEvent(5, new GoalWasScored(new Goal(4, $scorer, new ScoredAt(0)))),
                new VersionedEvent(6, new GoalWasScored(new Goal(5, $scorer, new ScoredAt(0)))),
                new VersionedEvent(7, new GoalWasScored(new Goal(6, $scorer, new ScoredAt(0)))),
                new VersionedEvent(8, new GoalWasScored(new Goal(7, $scorer, new ScoredAt(0)))),
                new VersionedEvent(9, new GoalWasScored(new Goal(8, $scorer, new ScoredAt(0)))),
                new VersionedEvent(10, new GoalWasScored(new Goal(9, $scorer, new ScoredAt(0)))),
                new VersionedEvent(11, new GoalWasScored(new Goal(10, $scorer, new ScoredAt(0)))),
                new VersionedEvent(12, new MatchWasWon('blue')),
            ])
        );

        $matchResult = new MatchResult(
            new WinningTeam('a', 'b'),
            new LosingTeam('c', 'd')
        );
        $eloScoresToReturn = [
            'a' => 1250,
            'b' => 1250,
            'c' => 1200,
            'd' => 1200,
        ];
        $updateEloScore = new FakeUpdateEloScore(new EloScores($matchResult, $eloScoresToReturn));
        $updateEloWhenMatchWonPolicy = new UpdateEloWhenMatchWonPolicy($matchRepository, $updateEloScore);

        // When
        $eloScores = $updateEloWhenMatchWonPolicy->on(new MatchSymfonyEvent($matchId, new MatchWasWon('blue')));

        // Then
        self::assertSame($eloScoresToReturn, $eloScores->playersWithScores());
        self::assertEquals($matchResult, $updateEloScore->getUsedMatchResult());
    }

    public function testShouldUpdateScoreWhenRedTeamWins() : void
    {
        // Given
        $matchId = new MatchId(Uuid::fromString('6df9c8af-afeb-4422-ac60-5f271c738d76'));
        $scorer = Scorer::fromTeamAndPosition('red', 'back');

        $teamBlue = FakeTeam::blue('a', 'b');
        $teamRed = FakeTeam::red('c', 'd');

        $matchRepository = Mockery::mock(MatchRepository::class);
        $matchRepository->allows('get')->with($matchId)->andReturns(
            Match::reconstituteFromHistory([
                new VersionedEvent(1, new MatchWasStarted(
                    $matchId,
                    $teamBlue,
                    $teamRed,
                    new DateTimeImmutable('2000-01-01 00:00:00')
                )),
                new VersionedEvent(2, new GoalWasScored(new Goal(1, $scorer, new ScoredAt(0)))),
                new VersionedEvent(3, new GoalWasScored(new Goal(2, $scorer, new ScoredAt(0)))),
                new VersionedEvent(4, new GoalWasScored(new Goal(3, $scorer, new ScoredAt(0)))),
                new VersionedEvent(5, new GoalWasScored(new Goal(4, $scorer, new ScoredAt(0)))),
                new VersionedEvent(6, new GoalWasScored(new Goal(5, $scorer, new ScoredAt(0)))),
                new VersionedEvent(7, new GoalWasScored(new Goal(6, $scorer, new ScoredAt(0)))),
                new VersionedEvent(8, new GoalWasScored(new Goal(7, $scorer, new ScoredAt(0)))),
                new VersionedEvent(9, new GoalWasScored(new Goal(8, $scorer, new ScoredAt(0)))),
                new VersionedEvent(10, new GoalWasScored(new Goal(9, $scorer, new ScoredAt(0)))),
                new VersionedEvent(11, new GoalWasScored(new Goal(10, $scorer, new ScoredAt(0)))),
                new VersionedEvent(12, new MatchWasWon('red')),
            ])
        );

        $matchResult = new MatchResult(
            new WinningTeam('c', 'd'),
            new LosingTeam('a', 'b')
        );
        $eloScoresToReturn = [
            'a' => 1250,
            'b' => 1250,
            'c' => 1200,
            'd' => 1200,
        ];
        $updateEloScore = new FakeUpdateEloScore(new EloScores($matchResult, $eloScoresToReturn));
        $updateEloWhenMatchWonPolicy = new UpdateEloWhenMatchWonPolicy($matchRepository, $updateEloScore);

        // When
        $eloScores = $updateEloWhenMatchWonPolicy->on(new MatchSymfonyEvent($matchId, new MatchWasWon('red')));

        // Then
        self::assertSame($eloScoresToReturn, $eloScores->playersWithScores());
        self::assertEquals($matchResult, $updateEloScore->getUsedMatchResult());
    }

    public function testShouldNotReactToOtherDomainEvents() : void
    {
        // Given
        $matchId = new MatchId(Uuid::fromString('6df9c8af-afeb-4422-ac60-5f271c738d76'));
        $scorer = Scorer::fromTeamAndPosition('blue', 'back');

        $teamBlue = FakeTeam::blue('a', 'b');
        $teamRed = FakeTeam::red('c', 'd');

        $matchRepository = Mockery::mock(MatchRepository::class);
        $matchRepository->allows('get')->with($matchId)->andReturns(
            Match::reconstituteFromHistory([
                new VersionedEvent(1, new MatchWasStarted(
                    $matchId,
                    $teamBlue,
                    $teamRed,
                    new DateTimeImmutable('2000-01-01 00:00:00')
                )),
            ])
        );

        $matchResult = new MatchResult(
            new WinningTeam('a', 'b'),
            new LosingTeam('c', 'd')
        );
        $eloScoresToReturn = [
            'a' => 1250,
            'b' => 1250,
            'c' => 1200,
            'd' => 1200,
        ];
        $updateEloScore = new FakeUpdateEloScore(new EloScores($matchResult, $eloScoresToReturn));
        $updateEloWhenMatchWonPolicy = new UpdateEloWhenMatchWonPolicy($matchRepository, $updateEloScore);

        // When
        $this->expectException(RuntimeException::class);
        $updateEloWhenMatchWonPolicy->on(
            new MatchSymfonyEvent($matchId, new GoalWasScored(new Goal(1, $scorer, new ScoredAt(0))))
        );
    }
}
