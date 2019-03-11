<?php

declare(strict_types=1);

namespace Fooscore\EventSubscriber;

use Fooscore\Controller\ApiController;
use Fooscore\Controller\HealthcheckController;
use Fooscore\Controller\IndexController;
use Fooscore\Identity\CanCheckToken;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

final class AuthTokenSubscriber implements EventSubscriberInterface
{
    /**
     * @var CanCheckToken
     */
    private $checkToken;

    /**
     * @var array
     */
    private $whitelist = [
        ApiController::class.'::login',
        IndexController::class.'::index',
        HealthcheckController::class.'::index',
    ];

    public function __construct(CanCheckToken $checkToken)
    {
        $this->checkToken = $checkToken;
    }

    public function onKernelRequest(GetResponseEvent $event): void
    {
        $action = $event->getRequest()->get('_controller');

        if (in_array($action, $this->whitelist, true)) {
            return;
        }

        $authToken = $event->getRequest()->headers->get('Authorization', '');

        if (!is_string($authToken) || $this->checkToken->isValid($authToken) === false) {
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
