<?php

declare(strict_types=1);

namespace Fooscore\Gaming\Adapters;

use Fooscore\Gaming\Match\GoalWasScored;
use Fooscore\Gaming\Match\MatchWasStarted;
use Fooscore\Gaming\Match\MatchWasWon;
use RuntimeException;

final class MatchDetailsProjector
{
    private const MINUTE_IN_SECONDS = 60;
    private const INITIAL_IS_WON_MATCH = false;
    private const INITIAL_SCORE = 0;

    /**
     * @var string
     */
    private $dir;

    public function __construct(string $dir)
    {
        $this->dir = $dir;
    }

    public function on(MatchSymfonyEvent $event): void
    {
        $domainEvent = $event->domainEvent();

        if ($domainEvent instanceof MatchWasStarted) {
            file_put_contents($this->dir.$event->matchId()->value()->toString().'.json', json_encode(
                [
                    'id' => $domainEvent->matchId()->value()->toString(),
                    'isWon' => self::INITIAL_IS_WON_MATCH,
                    'score' => [
                        'blue' => self::INITIAL_SCORE,
                        'red' => self::INITIAL_SCORE,
                    ],
                    'goals' => [],
                    'players' => [
                        'blue' => [
                            'back' => [
                                'id' => $domainEvent->teamBlue()->back(),
                                'name' => $domainEvent->teamBlue()->back(),
                            ],
                            'front' => [
                                'id' => $domainEvent->teamBlue()->front(),
                                'name' => $domainEvent->teamBlue()->front(),
                            ],
                        ],
                        'red' => [
                            'back' => [
                                'id' => $domainEvent->teamRed()->back(),
                                'name' => $domainEvent->teamRed()->back(),
                            ],
                            'front' => [
                                'id' => $domainEvent->teamRed()->front(),
                                'name' => $domainEvent->teamRed()->front(),
                            ],
                        ],
                    ],
                ],
                JSON_PRETTY_PRINT
            ));

            return;
        }

        if ($domainEvent instanceof GoalWasScored) {
            $content = $this->getFileContent($event);

            $matchState = json_decode($content, true);
            $goal = $domainEvent->goal();
            $matchState['goals'][] = [
                'id' => $goal->number(),
                'scoredAt' => [
                    'min' => floor($goal->scoredAt()->sec() / self::MINUTE_IN_SECONDS),
                    'sec' => $goal->scoredAt()->sec() % self::MINUTE_IN_SECONDS,
                ],
                'scorer' => [
                    'team' => $goal->scorer()->team(),
                    'position' => $goal->scorer()->position(),
                ],
            ];

            if ($goal->scorer()->team() === 'blue') {
                $matchState['score']['blue']++;
            } else {
                $matchState['score']['red']++;
            }

            file_put_contents($this->dir.$event->matchId()->value()->toString().'.json', json_encode(
                $matchState,
                JSON_PRETTY_PRINT
            ));

            return;
        }

        if ($domainEvent instanceof MatchWasWon) {
            $content = $this->getFileContent($event);

            $matchState = json_decode($content, true);

            $matchState['isWon'] = true;

            file_put_contents($this->dir.$event->matchId()->value()->toString().'.json', json_encode(
                $matchState,
                JSON_PRETTY_PRINT
            ));

            return;
        }

        throw new RuntimeException('Cannot find projector for event "'.$domainEvent::eventName().'"');
    }

    private function getFileContent(MatchSymfonyEvent $event): string
    {
        $content = @file_get_contents($this->dir.$event->matchId()->value()->toString().'.json');

        if ($content === false) {
            throw new RuntimeException('Cannot read projection.');
        }

        return $content;
    }
}
