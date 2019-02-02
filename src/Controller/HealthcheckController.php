<?php

namespace Fooscore\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HealthcheckController extends AbstractController
{
    /**
     * @Route("/status", name="healthcheck")
     */
    public function index()
    {
        return $this->json([
            'status' => 'ok',
        ]);
    }
}
