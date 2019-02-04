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
}
