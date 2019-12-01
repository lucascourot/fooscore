<?php

declare(strict_types=1);

namespace Fooscore\Tests\Integration\Gaming;

use DateTimeImmutable;
use Fooscore\Gaming\Infrastructure\Events\GoalWasScoredPublishedEvent;
use Fooscore\Gaming\Infrastructure\Events\MatchWasStartedPublishedEvent;
use Fooscore\Gaming\Infrastructure\Events\MatchWasWonPublishedEvent;
use Fooscore\Gaming\Infrastructure\Events\MiddlefieldGoalsWereValidatedByRegularGoalPublishedEvent;
use Fooscore\Gaming\Infrastructure\Events\MiddlefieldGoalWasScoredPublishedEvent;
use Fooscore\Gaming\Infrastructure\MatchDetailsProjector;
use Fooscore\Gaming\Match\Goal;
use Fooscore\Gaming\Match\GoalWasScored;
use Fooscore\Gaming\Match\MatchId;
use Fooscore\Gaming\Match\MatchWasStarted;
use Fooscore\Gaming\Match\MatchWasWon;
use Fooscore\Gaming\Match\MiddlefieldGoalsWereValidatedByRegularGoal;
use Fooscore\Gaming\Match\MiddlefieldGoalWasScored;
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
        $projector->onMatchWasStarted(new MatchWasStartedPublishedEvent($matchId, $matchWasStarted));

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

        $projector->onMatchWasStarted(new MatchWasStartedPublishedEvent(
            $matchId,
            new MatchWasStarted($matchId, $teamBlue, $teamRed, new DateTimeImmutable('2000-01-01 00:00:00'))
        ));

        // When
        $projector->onGoalWasScored(new GoalWasScoredPublishedEvent(
            $matchId,
            new GoalWasScored(new Goal(1, Scorer::fromTeamAndPosition('blue', 'back'), new ScoredAt(90)))
        ));
        $projector->onGoalWasScored(new GoalWasScoredPublishedEvent(
            $matchId,
            new GoalWasScored(new Goal(2, Scorer::fromTeamAndPosition('red', 'back'), new ScoredAt(90)))
        ));
        $projector->onGoalWasScored(new GoalWasScoredPublishedEvent(
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
            "type": "regular",
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
            "type": "regular",
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
            "type": "regular",
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

    public function testProjectMiddlefieldGoalWasScoredEvent() : void
    {
        // Given
        $projector = new MatchDetailsProjector($this->dir);
        $teamBlue = FakeTeam::blue('a', 'b');
        $teamRed = FakeTeam::red('c', 'd');
        $matchId = new MatchId(Uuid::fromString($this->testMatchId));

        $projector->onMatchWasStarted(new MatchWasStartedPublishedEvent(
            $matchId,
            new MatchWasStarted($matchId, $teamBlue, $teamRed, new DateTimeImmutable('2000-01-01 00:00:00'))
        ));

        // When
        $projector->onMiddlefieldGoalWasScored(new MiddlefieldGoalWasScoredPublishedEvent(
            $matchId,
            new MiddlefieldGoalWasScored(new Goal(1, Scorer::fromTeamAndPosition('blue', 'front'), new ScoredAt(90)))
        ));
        $projector->onMiddlefieldGoalWasScored(new MiddlefieldGoalWasScoredPublishedEvent(
            $matchId,
            new MiddlefieldGoalWasScored(new Goal(2, Scorer::fromTeamAndPosition('blue', 'front'), new ScoredAt(90)))
        ));
        $projector->onMiddlefieldGoalWasScored(new MiddlefieldGoalWasScoredPublishedEvent(
            $matchId,
            new MiddlefieldGoalWasScored(new Goal(3, Scorer::fromTeamAndPosition('blue', 'front'), new ScoredAt(90)))
        ));

        // Then
        self::assertJsonStringEqualsJsonFile($this->dir . $this->testMatchId . '.json', <<<JSON
{
    "id": "6df9c8af-afeb-4422-ac60-5f271c738d76",
    "isWon": false,
    "score": {
        "blue": 0,
        "red": 0
    },
    "goals": [
        {
            "id": 1,
            "type": "middlefield",
            "scoredAt": {
                "min": 1,
                "sec": 30
            },
            "scorer": {
                "team": "blue",
                "position": "front"
            }
        },
        {
            "id": 2,
            "type": "middlefield",
            "scoredAt": {
                "min": 1,
                "sec": 30
            },
            "scorer": {
                "team": "blue",
                "position": "front"
            }
        },
        {
            "id": 3,
            "type": "middlefield",
            "scoredAt": {
                "min": 1,
                "sec": 30
            },
            "scorer": {
                "team": "blue",
                "position": "front"
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

    public function testProjectMiddlefieldGoalsWereValidatedByRegularGoalPublishedEvent() : void
    {
        // Given
        $projector = new MatchDetailsProjector($this->dir);
        $teamBlue = FakeTeam::blue('a', 'b');
        $teamRed = FakeTeam::red('c', 'd');
        $matchId = new MatchId(Uuid::fromString($this->testMatchId));

        $projector->onMatchWasStarted(new MatchWasStartedPublishedEvent(
            $matchId,
            new MatchWasStarted($matchId, $teamBlue, $teamRed, new DateTimeImmutable('2000-01-01 00:00:00'))
        ));

        // When
        $projector->onGoalWasScored(new GoalWasScoredPublishedEvent(
            $matchId,
            new GoalWasScored(new Goal(1, Scorer::fromTeamAndPosition('blue', 'back'), new ScoredAt(90)))
        ));
        $projector->onMiddlefieldGoalWasScored(new MiddlefieldGoalWasScoredPublishedEvent(
            $matchId,
            new MiddlefieldGoalWasScored(new Goal(1, Scorer::fromTeamAndPosition('blue', 'front'), new ScoredAt(90)))
        ));
        $projector->onMiddlefieldGoalWasScored(new MiddlefieldGoalWasScoredPublishedEvent(
            $matchId,
            new MiddlefieldGoalWasScored(new Goal(2, Scorer::fromTeamAndPosition('blue', 'front'), new ScoredAt(90)))
        ));
        $projector->onMiddlefieldGoalsWereValidatedByRegularGoal(
            new MiddlefieldGoalsWereValidatedByRegularGoalPublishedEvent(
                $matchId,
                new MiddlefieldGoalsWereValidatedByRegularGoal(
                    new Goal(3, Scorer::fromTeamAndPosition('blue', 'front'), new ScoredAt(90)),
                    3
                )
            )
        );

        // Then
        self::assertJsonStringEqualsJsonFile($this->dir . $this->testMatchId . '.json', <<<JSON
{
    "id": "6df9c8af-afeb-4422-ac60-5f271c738d76",
    "isWon": false,
    "score": {
        "blue": 4,
        "red": 0
    },
    "goals": [
        {
            "id": 1,
            "type": "regular",
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
            "id": 1,
            "type": "middlefield",
            "scoredAt": {
                "min": 1,
                "sec": 30
            },
            "scorer": {
                "team": "blue",
                "position": "front"
            }
        },
        {
            "id": 2,
            "type": "middlefield",
            "scoredAt": {
                "min": 1,
                "sec": 30
            },
            "scorer": {
                "team": "blue",
                "position": "front"
            }
        },
        {
            "id": 3,
            "type": "middlefield_validated_by_regular",
            "numberOfGoalsToValidate": 3,
            "scoredAt": {
                "min": 1,
                "sec": 30
            },
            "scorer": {
                "team": "blue",
                "position": "front"
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

        $projector->onMatchWasStarted(new MatchWasStartedPublishedEvent(
            $matchId,
            new MatchWasStarted($matchId, $teamBlue, $teamRed, new DateTimeImmutable('2000-01-01 00:00:00'))
        ));
        foreach (range(1, 5) as $number) {
            $projector->onGoalWasScored(new GoalWasScoredPublishedEvent(
                $matchId,
                new GoalWasScored(new Goal($number, Scorer::fromTeamAndPosition('blue', 'back'), new ScoredAt(90)))
            ));
        }
        // When
        $projector->onMatchWasWon(new MatchWasWonPublishedEvent(
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
            "type": "regular",
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
            "type": "regular",
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
            "type": "regular",
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
            "type": "regular",
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
            "type": "regular",
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
        $projector->onGoalWasScored(new GoalWasScoredPublishedEvent($matchId, $goalWasScored));
    }
}
