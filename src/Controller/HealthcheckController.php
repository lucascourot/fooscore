<?php

declare(strict_types=1);

namespace Fooscore\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HealthcheckController extends AbstractController
{
    /**
     * @Route("/status", name="healthcheck")
     */
    public function index(): JsonResponse
    {
        return $this->json([
            'status' => 'ok',
        ]);
    }
}
