<?php

namespace Fooscore\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/login", name="api_login", methods={"POST"})
     */
    public function index()
    {
        return $this->json([
            'token' => 'abc',
        ]);
    }

    /**
     * @Route("/api/players", name="api_players", methods={"GET"})
     */
    public function players()
    {
        $players = [
            [
                'id' => '1',
                'name' => 'Lucas Courot',
            ],
            [
                'id' => '2',
                'name' => 'John Doe',
            ],
            [
                'id' => '3',
                'name' => 'Alice',
            ],
            [
                'id' => '4',
                'name' => 'Bob',
            ],
        ];

        return $this->json([
            'players' => $players,
        ]);
    }

    /**
     * @Route("/api/matches", name="api_start_match", methods={"POST"})
     */
    public function startMatch()
    {
        return $this->json([
            'id' => '123',
        ]);
    }

    /**
     * @Route("/api/matches/{matchId}/players", name="api_match_players", methods={"GET"})
     */
    public function matchPlayers()
    {
        $players = [
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
        ];

        return $this->json([
            'players' => $players,
        ]);
    }

    /**
     * @Route("/api/matches/{matchId}/players/{playerId}/goals", name="api_score_goal", methods={"POST"})
     */
    public function scoreGoal(string $matchId, string $playerId)
    {
        return $this->redirect($this->generateUrl('api_goal', [
            'matchId' => $matchId,
            'playerId' => $playerId,
            'goalId' => 'goal123',
        ]));
    }

    /**
     * @Route("/api/matches/{matchId}/players/{playerId}/goals/{goalId}", name="api_goal", methods={"GET"})
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
            ]
        ]);
    }
}
