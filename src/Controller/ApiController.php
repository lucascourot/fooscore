<?php

namespace Fooscore\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/login", name="api_login")
     */
    public function index()
    {
        return $this->json([
            'token' => 'abc',
        ]);
    }

    /**
     * @Route("/api/players", name="api_players")
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
}
