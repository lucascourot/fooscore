<?php

declare(strict_types=1);

namespace Fooscore\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * Redirects to game
     *
     * @Route("/", name="index")
     */
    public function index() : RedirectResponse
    {
        return $this->redirect('game');
    }
}
