<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Gaming;

use Fooscore\Gaming\Match\{
    Goal, GoalWasScored, Match, MatchId, MatchIdGenerator, MatchRepository, MatchWasStarted, ScoredAt, Scorer, StartMatch, TeamBlue, TeamRed, VersionedEvent
};
use Fooscore\Gaming\MatchDetails\ShowMatchDetails;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @group unit
 *
 * In order to follow match progression
 * As a referee, a player or a spectator
 * I want to see the match details
 */
class ShowMatchDetailsTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testShouldShowDetailsOfJustStartedMatch(): void
    {
        // Given
        $teamBlue = new TeamBlue('a', 'b');
        $teamRed = new TeamRed('c', 'd');
        $matchId = new MatchId(Uuid::fromString('6df9c8af-afeb-4422-ac60-5f271c738d76'));
        $matchRepository = Mockery::mock(MatchRepository::class, [
            'get' => Match::reconstituteFromHistory([
                new VersionedEvent(1, new MatchWasStarted($matchId, $teamBlue, $teamRed, new \DateTimeImmutable('2000-01-01 00:00:00'))),
            ]),
        ]);

        // When
        $showMatchDetails = new ShowMatchDetails($matchRepository);
        $match = $showMatchDetails->showMatchDetails($matchId);

        // Then
        self::assertEquals([
            'id' => $matchId->value()->toString(),
            'isWon' => false,
            'score' => [
                'blue' => 0,
                'red' => 0,
            ],
            'goals' => [],
            'players' => [
                'blue' => [
                    'back' => [
                        'id' => $teamBlue->back(),
                        'name' => $teamBlue->back(),
                    ],
                    'front' => [
                        'id' => $teamBlue->front(),
                        'name' => $teamBlue->front(),
                    ],
                ],
                'red' => [
                    'back' => [
                        'id' => $teamRed->back(),
                        'name' => $teamRed->back(),
                    ],
                    'front' => [
                        'id' => $teamRed->front(),
                        'name' => $teamRed->front(),
                    ],
                ],
            ],
        ], $match->details());
    }

    public function testShouldShowDetailsOfScoredRegularGoal(): void
    {
        // Given
        $teamBlue = new TeamBlue('a', 'b');
        $teamRed = new TeamRed('c', 'd');
        $matchId = new MatchId(Uuid::fromString('6df9c8af-afeb-4422-ac60-5f271c738d76'));
        $matchRepository = Mockery::mock(MatchRepository::class, [
            'get' => Match::reconstituteFromHistory([
                new VersionedEvent(1, new MatchWasStarted($matchId, $teamBlue, $teamRed, new \DateTimeImmutable('2000-01-01 00:00:00'))),
                new VersionedEvent(2, new GoalWasScored(new Goal(1, Scorer::fromTeamAndPosition('blue', 'back'), new ScoredAt(90)))),
                new VersionedEvent(3, new GoalWasScored(new Goal(2, Scorer::fromTeamAndPosition('red', 'back'), new ScoredAt(90)))),
                new VersionedEvent(4, new GoalWasScored(new Goal(3, Scorer::fromTeamAndPosition('red', 'back'), new ScoredAt(90)))),
            ]),
        ]);

        // When
        $showMatchDetails = new ShowMatchDetails($matchRepository);
        $match = $showMatchDetails->showMatchDetails($matchId);

        // Then
        self::assertEquals([
            'id' => $matchId->value()->toString(),
            'isWon' => false,
            'score' => [
                'blue' => 1,
                'red' => 2,
            ],
            'goals' => [
                [
                    'id' => 1,
                    'scoredAt' => [
                        'min' => 1,
                        'sec' => 30,
                    ],
                    'scorer' => [
                        'team' => 'blue',
                        'position' => 'back',
                    ],
                ],
                [
                    'id' => 2,
                    'scoredAt' => [
                        'min' => 1,
                        'sec' => 30,
                    ],
                    'scorer' => [
                        'team' => 'red',
                        'position' => 'back',
                    ],
                ],
                [
                    'id' => 3,
                    'scoredAt' => [
                        'min' => 1,
                        'sec' => 30,
                    ],
                    'scorer' => [
                        'team' => 'red',
                        'position' => 'back',
                    ],
                ],
            ],
            'players' => [
                'blue' => [
                    'back' => [
                        'id' => $teamBlue->back(),
                        'name' => $teamBlue->back(),
                    ],
                    'front' => [
                        'id' => $teamBlue->front(),
                        'name' => $teamBlue->front(),
                    ],
                ],
                'red' => [
                    'back' => [
                        'id' => $teamRed->back(),
                        'name' => $teamRed->back(),
                    ],
                    'front' => [
                        'id' => $teamRed->front(),
                        'name' => $teamRed->front(),
                    ],
                ],
            ],
        ], $match->details());
    }

    public function testShouldThrowExceptionWhenFetchingLastGoalAndNoScoredGoalYet(): void
    {
        $this->expectException(\RuntimeException::class);

        // Given
        $teamBlue = new TeamBlue('a', 'b');
        $teamRed = new TeamRed('c', 'd');
        $matchId = new MatchId(Uuid::fromString('6df9c8af-afeb-4422-ac60-5f271c738d76'));
        $matchRepository = Mockery::mock(MatchRepository::class, [
            'get' => Match::reconstituteFromHistory([
                new VersionedEvent(1, new MatchWasStarted($matchId, $teamBlue, $teamRed, new \DateTimeImmutable('2000-01-01 00:00:00'))),
            ]),
        ]);

        // When
        $showMatchDetails = new ShowMatchDetails($matchRepository);
        $matchWithDetails = $showMatchDetails->showMatchDetails($matchId);

        $matchWithDetails->lastScoredGoal();
    }

    public function testShouldReturnLastScoredGoal(): void
    {
        // Given
        $teamBlue = new TeamBlue('a', 'b');
        $teamRed = new TeamRed('c', 'd');
        $matchId = new MatchId(Uuid::fromString('6df9c8af-afeb-4422-ac60-5f271c738d76'));
        $matchRepository = Mockery::mock(MatchRepository::class, [
            'get' => Match::reconstituteFromHistory([
                new VersionedEvent(1, new MatchWasStarted($matchId, $teamBlue, $teamRed, new \DateTimeImmutable('2000-01-01 00:00:00'))),
                new VersionedEvent(2, new GoalWasScored(
                        new Goal(1, Scorer::fromTeamAndPosition('blue', 'back'), new ScoredAt(90))
                    )
                ),
                new VersionedEvent(3, new GoalWasScored(
                        new Goal(2, Scorer::fromTeamAndPosition('red', 'back'), new ScoredAt(90))
                    )
                ),
            ]),
        ]);

        // When
        $showMatchDetails = new ShowMatchDetails($matchRepository);
        $matchWithDetails = $showMatchDetails->showMatchDetails($matchId);

        // Then
        self::assertEquals(
            new Goal(2, Scorer::fromTeamAndPosition('red', 'back'), new ScoredAt(90)),
            $matchWithDetails->lastScoredGoal()
        );
    }
}
