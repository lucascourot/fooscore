<?php

namespace Fooscore\EventSubscriber;

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

    public function __construct(CheckToken $checkToken)
    {
        $this->checkToken = $checkToken;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
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
