<?php

declare(strict_types=1);

namespace Fooscore\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class ApiExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $env;

    public function __construct(LoggerInterface $logger, string $env)
    {
        $this->logger = $logger;
        $this->env = $env;
    }

    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $thrownException = $event->getException();

        $statusCode = $this->getStatusCodeFromException($thrownException);

        $response = new JsonResponse([
            'error' => $statusCode,
            'message' => $this->env === 'prod' ? Response::$statusTexts[$statusCode] : $thrownException->getMessage(),
        ], $statusCode);

        $this->logger->error(
            sprintf(
                'Uncaught PHP Exception %s: "%s" at %s line %s',
                get_class($thrownException),
                $thrownException->getMessage(),
                $thrownException->getFile(),
                $thrownException->getLine()
            ),
            [
                'trace' => $thrownException->getTrace(),
                'request' => $event->getRequest(),
                'project' => 'service-6play-layout',
                'uri' => $event->getRequest()->getRequestUri(),
                'customerCode' => $event->getRequest()->attributes->get('customerCode'),
                'platform' => $event->getRequest()->attributes->get('platform'),
            ]
        );

        // Sets the response and *stops propagation*
        // Replaces the Symfony\Component\HttpKernel\EventListener\ExceptionListener
        $event->setResponse($response);
    }

    private function getStatusCodeFromException(\Throwable $thrownException): int
    {
        if ($thrownException instanceof HttpExceptionInterface) {
            return $thrownException->getStatusCode();
        }

        return $thrownException instanceof \InvalidArgumentException ? Response::HTTP_BAD_REQUEST : Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
