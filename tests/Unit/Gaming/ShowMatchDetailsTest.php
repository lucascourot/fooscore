<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Gaming;

use Fooscore\Gaming\Match\{
    Goal, GoalWasScored, Match, MatchId, MatchIdGenerator, MatchRepository, MatchWasStarted, ScoredAt, Scorer, ShowMatchDetails, StartMatch, TeamBlue, TeamRed, VersionedEvent
};
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
                new VersionedEvent(2, new GoalWasScored(new Goal(1, Scorer::fromTeamAndPosition('blue', 'back'), new ScoredAt(1, 30)))),
            ]),
        ]);

        // When
        $showMatchDetails = new ShowMatchDetails($matchRepository);
        $match = $showMatchDetails->showMatchDetails($matchId);

        // Then
        self::assertEquals([
            'id' => $matchId->value()->toString(),
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
}
