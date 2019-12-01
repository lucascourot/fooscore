<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Infrastructure;

use Fooscore\Gaming\Infrastructure\Events\PublishedEvent;
use RuntimeException;
use const JSON_PRETTY_PRINT;
use function file_get_contents;
use function file_put_contents;
use function floor;
use function json_decode;
use function json_encode;

final class MatchDetailsProjector
{
    private const MINUTE_IN_SECONDS = 60;
    private const INITIAL_IS_MATCH_FINISHED = false;
    private const INITIAL_SCORE = 0;

    /** @var string */
    private $dir;

    public function __construct(string $projectionDir)
    {
        $this->dir = $projectionDir;
    }

    public function onMatchWasStarted(Events\MatchWasStartedPublishedEvent $event) : void
    {
        $domainEvent = $event->domainEvent();

        file_put_contents($this->dir . $event->matchId()->value()->toString() . '.json', json_encode(
            [
                'id' => $domainEvent->matchId()->value()->toString(),
                'isWon' => self::INITIAL_IS_MATCH_FINISHED,
                'score' => [
                    'blue' => self::INITIAL_SCORE,
                    'red' => self::INITIAL_SCORE,
                ],
                'goals' => [],
                'players' => [
                    'blue' => [
                        'back' => [
                            'id' => $domainEvent->teamBlue()->back()->id(),
                            'name' => $domainEvent->teamBlue()->back()->name(),
                        ],
                        'front' => [
                            'id' => $domainEvent->teamBlue()->front()->id(),
                            'name' => $domainEvent->teamBlue()->front()->name(),
                        ],
                    ],
                    'red' => [
                        'back' => [
                            'id' => $domainEvent->teamRed()->back()->id(),
                            'name' => $domainEvent->teamRed()->back()->name(),
                        ],
                        'front' => [
                            'id' => $domainEvent->teamRed()->front()->id(),
                            'name' => $domainEvent->teamRed()->front()->name(),
                        ],
                    ],
                ],
            ],
            JSON_PRETTY_PRINT
        ));
    }

    public function onGoalWasScored(Events\GoalWasScoredPublishedEvent $event) : void
    {
        $domainEvent = $event->domainEvent();
        $content = $this->getFileContent($event);

        $matchState = json_decode($content, true);
        $goal = $domainEvent->goal();
        $matchState['goals'][] = [
            'id' => $goal->number(),
            'type' => 'regular',
            'scoredAt' => [
                'min' => floor($goal->scoredAt()->sec() / self::MINUTE_IN_SECONDS),
                'sec' => $goal->scoredAt()->sec() % self::MINUTE_IN_SECONDS,
            ],
            'scorer' => [
                'team' => $goal->scorer()->team(),
                'position' => $goal->scorer()->position(),
            ],
        ];

        $matchState['score'][$goal->scorer()->team()]++;

        file_put_contents($this->dir . $event->matchId()->value()->toString() . '.json', json_encode(
            $matchState,
            JSON_PRETTY_PRINT
        ));
    }

    public function onMiddlefieldGoalWasScored(Events\MiddlefieldGoalWasScoredPublishedEvent $event) : void
    {
        $domainEvent = $event->domainEvent();
        $content = $this->getFileContent($event);

        $matchState = json_decode($content, true);
        $goal = $domainEvent->goal();
        $matchState['goals'][] = [
            'id' => $goal->number(),
            'type' => 'middlefield',
            'scoredAt' => [
                'min' => floor($goal->scoredAt()->sec() / self::MINUTE_IN_SECONDS),
                'sec' => $goal->scoredAt()->sec() % self::MINUTE_IN_SECONDS,
            ],
            'scorer' => [
                'team' => $goal->scorer()->team(),
                'position' => $goal->scorer()->position(),
            ],
        ];

        file_put_contents($this->dir . $event->matchId()->value()->toString() . '.json', json_encode(
            $matchState,
            JSON_PRETTY_PRINT
        ));
    }

    public function onMiddlefieldGoalsWereValidatedByRegularGoal(
        Events\MiddlefieldGoalsWereValidatedByRegularGoalPublishedEvent $event
    ) : void {
        $domainEvent = $event->domainEvent();
        $content = $this->getFileContent($event);

        $matchState = json_decode($content, true);
        $goal = $domainEvent->goal();
        $matchState['goals'][] = [
            'id' => $goal->number(),
            'type' => 'middlefield_validated_by_regular',
            'numberOfGoalsToValidate' => $domainEvent->numberOfGoalsToValidate(),
            'scoredAt' => [
                'min' => floor($goal->scoredAt()->sec() / self::MINUTE_IN_SECONDS),
                'sec' => $goal->scoredAt()->sec() % self::MINUTE_IN_SECONDS,
            ],
            'scorer' => [
                'team' => $goal->scorer()->team(),
                'position' => $goal->scorer()->position(),
            ],
        ];

        $matchState['score'][$goal->scorer()->team()] += $domainEvent->numberOfGoalsToValidate();

        file_put_contents($this->dir . $event->matchId()->value()->toString() . '.json', json_encode(
            $matchState,
            JSON_PRETTY_PRINT
        ));
    }

    public function onMatchWasWon(Events\MatchWasWonPublishedEvent $event) : void
    {
        $content = $this->getFileContent($event);

        $matchState = json_decode($content, true);

        $matchState['isWon'] = true;

        file_put_contents($this->dir . $event->matchId()->value()->toString() . '.json', json_encode(
            $matchState,
            JSON_PRETTY_PRINT
        ));
    }

    private function getFileContent(PublishedEvent $event) : string
    {
        $content = @file_get_contents($this->dir . $event->matchId()->value()->toString() . '.json');

        if ($content === false) {
            throw new RuntimeException('Cannot read projection.');
        }

        return $content;
    }
}
