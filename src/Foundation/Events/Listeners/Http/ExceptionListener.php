<?php

declare(strict_types=1);

namespace Framewire\Foundation\Events\Listeners\Http;

use Framewire\Contracts\Event\EventListenerInterface;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Psr\EventDispatcher\StoppableEventInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionListener implements ContainerAwareInterface, EventListenerInterface
{
    use ContainerAwareTrait;

    public function handle(ExceptionEvent|StoppableEventInterface $event): void
    {
        $throwable = $event->getThrowable();

        do {
            if ($throwable instanceof BadRequestHttpException) {
                $this->onBadRequestException($event, $throwable);

                return;
            }

            if ($throwable instanceof MethodNotAllowedHttpException) {
                $this->onMethodNotAllowedException($event, $throwable);

                return;
            }

            if ($throwable instanceof  NotFoundHttpException) {
                $this->onNotFoundException($event, $throwable);

                return;
            }
        } while (null !== $throwable = $throwable->getPrevious());
    }

    private function onBadRequestException(ExceptionEvent $event, BadRequestHttpException $exception): void
    {
        $event->setResponse(new Response($exception->getMessage(), Response::HTTP_BAD_REQUEST));
    }

    private function onMethodNotAllowedException(ExceptionEvent $event, MethodNotAllowedHttpException $exception): void
    {
        $event->setResponse(new Response($exception->getMessage(), Response::HTTP_METHOD_NOT_ALLOWED));
    }
    private function onNotFoundException(ExceptionEvent $event, NotFoundHttpException $exception): void
    {
        $event->setResponse(new Response($exception->getMessage(), Response::HTTP_NOT_FOUND));
    }
}
