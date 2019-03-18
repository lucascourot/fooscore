<?php

declare(strict_types=1);

namespace Fooscore\Tests\Integration\Gaming;

use DateTimeImmutable;
use Fooscore\Gaming\Infrastructure\MatchDetailsProjector;
use Fooscore\Gaming\Infrastructure\MatchSymfonyEvent;
use Fooscore\Gaming\Match\DomainEvent;
use Fooscore\Gaming\Match\Goal;
use Fooscore\Gaming\Match\GoalWasScored;
use Fooscore\Gaming\Match\MatchId;
use Fooscore\Gaming\Match\MatchWasStarted;
use Fooscore\Gaming\Match\MatchWasWon;
use Fooscore\Gaming\Match\ScoredAt;
use Fooscore\Gaming\Match\Scorer;
use Fooscore\Tests\Unit\Gaming\FakeTeam;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Ramsey\Uuid\Uuid;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use const DIRECTORY_SEPARATOR;
use function range;
use function unlink;

/**
 * @group integration
 */
class MatchDetailsProjectorTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    /** @var string */
    private $testMatchId = '6df9c8af-afeb-4422-ac60-5f271c738d76';

    /** @var string */
    private $dir;

    protected function setUp() : void
    {
        $kernel = self::bootKernel();
        $this->dir = $kernel->getProjectDir() . DIRECTORY_SEPARATOR . 'var/';
    }

    protected function tearDown() : void
    {
        parent::tearDown();

        @unlink($this->dir . $this->testMatchId . '.json');
    }

    public function testProjectMatchWasStartedEvent() : void
    {
        // Given
        $projector = new MatchDetailsProjector($this->dir);
        $teamBlue = FakeTeam::blue('a', 'b');
        $teamRed = FakeTeam::red('c', 'd');
        $matchId = new MatchId(Uuid::fromString($this->testMatchId));

        $matchWasStarted = new MatchWasStarted(
            $matchId,
            $teamBlue,
            $teamRed,
            new DateTimeImmutable()
        );

        // When
        $projector->on(new MatchSymfonyEvent($matchId, $matchWasStarted));

        // Then
        self::assertJsonStringEqualsJsonFile($this->dir . $this->testMatchId . '.json', <<<JSON
{
    "id": "6df9c8af-afeb-4422-ac60-5f271c738d76",
    "isWon": false,
    "score": {
        "blue": 0,
        "red": 0
    },
    "goals": [],
    "players": {
        "blue": {
            "back": {
                "id": "a",
                "name": "a"
            },
            "front": {
                "id": "b",
                "name": "b"
            }
        },
        "red": {
            "back": {
                "id": "c",
                "name": "c"
            },
            "front": {
                "id": "d",
                "name": "d"
            }
        }
    }
}
JSON
        );
    }

    public function testProjectGoalWasScoredEvent() : void
    {
        // Given
        $projector = new MatchDetailsProjector($this->dir);
        $teamBlue = FakeTeam::blue('a', 'b');
        $teamRed = FakeTeam::red('c', 'd');
        $matchId = new MatchId(Uuid::fromString($this->testMatchId));

        $projector->on(new MatchSymfonyEvent(
            $matchId,
            new MatchWasStarted($matchId, $teamBlue, $teamRed, new DateTimeImmutable('2000-01-01 00:00:00'))
        ));

        // When
        $projector->on(new MatchSymfonyEvent(
            $matchId,
            new GoalWasScored(new Goal(1, Scorer::fromTeamAndPosition('blue', 'back'), new ScoredAt(90)))
        ));
        $projector->on(new MatchSymfonyEvent(
            $matchId,
            new GoalWasScored(new Goal(2, Scorer::fromTeamAndPosition('red', 'back'), new ScoredAt(90)))
        ));
        $projector->on(new MatchSymfonyEvent(
            $matchId,
            new GoalWasScored(new Goal(3, Scorer::fromTeamAndPosition('red', 'back'), new ScoredAt(90)))
        ));

        // Then
        self::assertJsonStringEqualsJsonFile($this->dir . $this->testMatchId . '.json', <<<JSON
{
    "id": "6df9c8af-afeb-4422-ac60-5f271c738d76",
    "isWon": false,
    "score": {
        "blue": 1,
        "red": 2
    },
    "goals": [
        {
            "id": 1,
            "scoredAt": {
                "min": 1,
                "sec": 30
            },
            "scorer": {
                "team": "blue",
                "position": "back"
            }
        },
        {
            "id": 2,
            "scoredAt": {
                "min": 1,
                "sec": 30
            },
            "scorer": {
                "team": "red",
                "position": "back"
            }
        },
        {
            "id": 3,
            "scoredAt": {
                "min": 1,
                "sec": 30
            },
            "scorer": {
                "team": "red",
                "position": "back"
            }
        }
    ],
    "players": {
        "blue": {
            "back": {
                "id": "a",
                "name": "a"
            },
            "front": {
                "id": "b",
                "name": "b"
            }
        },
        "red": {
            "back": {
                "id": "c",
                "name": "c"
            },
            "front": {
                "id": "d",
                "name": "d"
            }
        }
    }
}
JSON
        );
    }

    public function testProjectMatchWasWon() : void
    {
        // Given
        $projector = new MatchDetailsProjector($this->dir);
        $teamBlue = FakeTeam::blue('a', 'b');
        $teamRed = FakeTeam::red('c', 'd');
        $matchId = new MatchId(Uuid::fromString($this->testMatchId));

        $projector->on(new MatchSymfonyEvent(
            $matchId,
            new MatchWasStarted($matchId, $teamBlue, $teamRed, new DateTimeImmutable('2000-01-01 00:00:00'))
        ));
        foreach (range(1, 5) as $number) {
            $projector->on(new MatchSymfonyEvent(
                $matchId,
                new GoalWasScored(new Goal($number, Scorer::fromTeamAndPosition('blue', 'back'), new ScoredAt(90)))
            ));
        }
        // When
        $projector->on(new MatchSymfonyEvent(
            $matchId,
            new MatchWasWon('blue')
        ));

        // Then
        self::assertJsonStringEqualsJsonFile($this->dir . $this->testMatchId . '.json', <<<JSON
{
    "id": "6df9c8af-afeb-4422-ac60-5f271c738d76",
    "isWon": true,
    "score": {
        "blue": 5,
        "red": 0
    },
    "goals": [
        {
            "id": 1,
            "scoredAt": {
                "min": 1,
                "sec": 30
            },
            "scorer": {
                "team": "blue",
                "position": "back"
            }
        },
        {
            "id": 2,
            "scoredAt": {
                "min": 1,
                "sec": 30
            },
            "scorer": {
                "team": "blue",
                "position": "back"
            }
        },
        {
            "id": 3,
            "scoredAt": {
                "min": 1,
                "sec": 30
            },
            "scorer": {
                "team": "blue",
                "position": "back"
            }
        },
        {
            "id": 4,
            "scoredAt": {
                "min": 1,
                "sec": 30
            },
            "scorer": {
                "team": "blue",
                "position": "back"
            }
        },
        {
            "id": 5,
            "scoredAt": {
                "min": 1,
                "sec": 30
            },
            "scorer": {
                "team": "blue",
                "position": "back"
            }
        }
    ],
    "players": {
        "blue": {
            "back": {
                "id": "a",
                "name": "a"
            },
            "front": {
                "id": "b",
                "name": "b"
            }
        },
        "red": {
            "back": {
                "id": "c",
                "name": "c"
            },
            "front": {
                "id": "d",
                "name": "d"
            }
        }
    }
}
JSON
        );
    }

    public function testCannotReadProjection() : void
    {
        $this->expectException(RuntimeException::class);

        // Given
        $projector = new MatchDetailsProjector($this->dir);
        $matchId = new MatchId(Uuid::fromString($this->testMatchId));

        // When
        $goalWasScored = new GoalWasScored(
            new Goal(1, Scorer::fromTeamAndPosition('blue', 'front'), new ScoredAt(70))
        );
        $projector->on(new MatchSymfonyEvent($matchId, $goalWasScored));
    }

    public function testCannotProjectUnknownEvent() : void
    {
        $this->expectException(RuntimeException::class);

        // Given
        $projector = new MatchDetailsProjector($this->dir);
        $matchId = new MatchId(Uuid::fromString($this->testMatchId));

        // When
        $unknownEvent = new class() implements DomainEvent {
            public static function eventName() : string
            {
                return 'error';
            }

            /**
             * {@inheritdoc}
             */
            public static function fromEventDataArray(array $eventData) : DomainEvent
            {
                return new self();
            }

            /**
             * {@inheritdoc}
             */
            public function eventDataAsArray() : array
            {
                return [];
            }
        };
        $projector->on(new MatchSymfonyEvent($matchId, $unknownEvent));
    }
}
