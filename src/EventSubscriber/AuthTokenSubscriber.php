<?php

namespace Fooscore\EventSubscriber;

use Fooscore\Controller\ApiController;
use Fooscore\Controller\HealthcheckController;
use Fooscore\Controller\IndexController;
use Fooscore\Identity\CheckToken;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

final class AuthTokenSubscriber implements EventSubscriberInterface
{
    /**
     * @var CheckToken
     */
    private $checkToken;

    private $whitelist = [
        ApiController::class.'::index',
        IndexController::class.'::index',
        HealthcheckController::class.'::index',
    ];

    public function __construct(CheckToken $checkToken)
    {
        $this->checkToken = $checkToken;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $action = $event->getRequest()->get('_controller');

        if (in_array($action, $this->whitelist)) {
            return;
        }

        $authToken = $event->getRequest()->headers->get('Authorization', '');

        if ($this->checkToken->isValid($authToken) === false) {
            $event->setResponse(
                new JsonResponse([
                    'error' => 'Invalid auth token.',
                ], Response::HTTP_FORBIDDEN)
            );
        }
    }

    public static function getSubscribedEvents()
    {
        return [
           'kernel.request' => 'onKernelRequest',
        ];
    }
}
