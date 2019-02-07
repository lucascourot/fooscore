<?php

namespace Fooscore\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
                'displayName' => 'Lucas C.',
            ],
            [
                'id' => '2',
                'name' => 'John Doe',
                'displayName' => 'John D.',
            ],
            [
                'id' => '3',
                'name' => 'Alice',
                'displayName' => 'Alice',
            ],
            [
                'id' => '4',
                'name' => 'Bob',
                'displayName' => 'Bob',
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
                    'displayName' => 'Lucas C.',
                ],
                'front' => [
                    'id' => '2',
                    'displayName' => 'John D.',
                ],
            ],
            'red' => [
                'back' => [
                    'id' => '3',
                    'displayName' => 'Alice',
                ],
                'front' => [
                    'id' => '4',
                    'displayName' => 'Bob',
                ],
            ],
        ];

        return $this->json([
            'players' => $players,
        ]);
    }
}
