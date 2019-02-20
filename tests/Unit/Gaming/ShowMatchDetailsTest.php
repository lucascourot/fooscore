<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Gaming;

use Fooscore\Gaming\Match\{
    Match, MatchId, MatchIdGenerator, MatchRepository, MatchWasStarted, ShowMatchDetails, TeamBlue, TeamRed, UseCaseStartMatch, VersionedEvent
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
                new VersionedEvent(1, new MatchWasStarted($matchId, $teamBlue, $teamRed)),
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
}
