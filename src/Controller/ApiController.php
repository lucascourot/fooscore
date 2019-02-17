<?php

declare(strict_types=1);

namespace Fooscore\Controller;

use Fooscore\Gaming\Match\MatchId;
use Fooscore\Gaming\Match\Scorer;
use Fooscore\Gaming\Match\TeamBlue;
use Fooscore\Gaming\Match\TeamRed;
use Fooscore\Gaming\ScoreGoal;
use Fooscore\Gaming\StartMatch;
use Fooscore\Identity\Credentials;
use Fooscore\Identity\GetUsers;
use Fooscore\Identity\LogIn;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/login", name="api_login", methods={"POST"})
     */
    public function index(Request $request, LogIn $logIn)
    {
        $credentials = json_decode((string) $request->getContent(), true);
        $token = $logIn->logIn(
            new Credentials(
                $credentials['username'] ?? '',
                $credentials['password'] ?? ''
            )
        );

        if ($token === null) {
            return $this->json([
                'error' => 'Cannot log in the user.',
            ], 400);
        }

        return $this->json([
            'token' => $token,
        ]);
    }

    /**
     * @Route("/api/players", name="api_players", methods={"GET"})
     */
    public function players(GetUsers $getUsers)
    {
        return $this->json([
            'players' => $getUsers->getUsers(),
        ]);
    }

    /**
     * @Route("/api/matches", name="api_start_match", methods={"POST"})
     */
    public function startMatch(Request $request, StartMatch $startMatch)
    {
        $players = json_decode((string) $request->getContent(), true)['players'];

        $match = $startMatch->startMatch(
            new TeamBlue($players['blueBack'], $players['blueFront']),
            new TeamRed($players['redBack'], $players['redFront'])
        );

        return $this->redirect($this->generateUrl('api_match', [
            'matchId' => $match->id()->value()->toString(),
        ]));
    }

    /**
     * @Route("/api/matches/{matchId}", name="api_match", methods={"GET"})
     */
    public function showMatch(string $matchId)
    {
        return $this->json([
            'id' => $matchId,
            'goals' => [
                [
                    'id' => 1,
                    'scoredAt' => [
                        'min' => 1,
                        'sec' => 40,
                    ],
                    'author' => [
                        'name' => 'Lucas Courot',
                        'team' => 'blue',
                        'position' => 'back',
                    ],
                ],
                [
                    'id' => 2,
                    'scoredAt' => [
                        'min' => 10,
                        'sec' => 05,
                    ],
                    'author' => [
                        'name' => 'Alice',
                        'team' => 'red',
                        'position' => 'back',
                    ],
                ],
            ],
            'players' => [
                'blue' => [
                    'back' => [
                        'id' => '1',
                        'name' => 'Lucas Courot',
                    ],
                    'front' => [
                        'id' => '2',
                        'name' => 'John Doe',
                    ],
                ],
                'red' => [
                    'back' => [
                        'id' => '3',
                        'name' => 'Alice',
                    ],
                    'front' => [
                        'id' => '4',
                        'name' => 'Bob',
                    ],
                ],
            ],
        ]);
    }

    /**
     * @Route("/api/matches/{matchId}/goals", name="api_score_goal", methods={"POST"})
     */
    public function scoreGoal(Request $request, string $matchId, ScoreGoal $scoreGoal)
    {
        $content = json_decode((string) $request->getContent(), true);
        $type = $content['type'];
        $team = $content['team'];
        $position = $content['position'];

        $scoreGoal->scoreGoal(
            new MatchId(Uuid::fromString($matchId)),
            Scorer::fromTeamAndPosition($team, $position)
        );

        return $this->redirect($this->generateUrl('api_goal', [
            'matchId' => $matchId,
            'goalId' => 'goal123',
        ]));
    }

    /**
     * @Route("/api/matches/{matchId}/goals/{goalId}", name="api_goal", methods={"GET"})
     */
    public function showGoal(string $goalId)
    {
        return $this->json([
            'id' => $goalId,
            'scoredAt' => [
                'min' => 1,
                'sec' => 40,
            ],
            'author' => [
                'name' => 'Lucas Courot',
                'team' => 'blue',
                'position' => 'back',
            ],
        ]);
    }
}
