<?php

declare(strict_types=1);

namespace Framewire\Foundation\Events\Listeners\Http;

use Framewire\Contracts\Event\EventListenerInterface;
use Framewire\Enum\Filesystem\Driver;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckExistence;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionListener implements ContainerAwareInterface, EventListenerInterface
{
    use ContainerAwareTrait;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
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
                $this->onNotFoundException(
                    $event->getRequest(),
                    $this->getContainer()->get(Driver::PUBLIC->value),
                    $event
                );

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
    private function onNotFoundException(Request $request, Filesystem $filesystem, ExceptionEvent $event): void
    {
        $path = $request->getPathInfo();

        try {
            if ($filesystem->has($path)){
                $event->setResponse(new BinaryFileResponse($path));
            }

            $extension = pathinfo($path, PATHINFO_EXTENSION);

            if (empty($extension)) {
                $event->setResponse(new Response($event->getThrowable()->getMessage(), Response::HTTP_NOT_FOUND));
            } else {
                $message = sprintf(
                    'No file found for "%s %s"',
                    $request->getMethod(),
                    $request->getUriForPath($request->getPathInfo())
                );
                if ($referer = $request->headers->get('referer')) {
                    $message .= sprintf(' (from "%s")', $referer);
                }
                $event->setResponse(new Response($message, Response::HTTP_NOT_FOUND));
            }
        } catch (FilesystemException|FileNotFoundException|UnableToCheckExistence) {
            // TODO: Humanize error information
            $message = sprintf(
                'No file found for "%s %s"',
                $request->getMethod(),
                $request->getUriForPath($request->getPathInfo())
            );

            if ($referer = $request->headers->get('referer')) {
                $message .= sprintf(' (from "%s")', $referer);
            }
            $event->setResponse(new Response($message, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }
}
