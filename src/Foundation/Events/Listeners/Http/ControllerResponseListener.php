<?php

namespace Framewire\Foundation\Events\Listeners\Http;

use Framewire\Contracts\Event\EventListenerInterface;
use Framewire\Contracts\View\TemplateInterface;
use Framewire\Inertia\Contracts\InertiaInterface;
use Framewire\Inertia\Core\Inertia;
use Latte\Engine;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use ReflectionException;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class ControllerResponseListener implements EventListenerInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|ReflectionException
     */
    public function handle(StoppableEventInterface|ViewEvent $event): void
    {
        $request = $event->getRequest();
        $view = $event->getControllerResult();

        /**
         * @var $inertia Inertia
         */
        $inertia = $this->getContainer()->get(InertiaInterface::class)->setRequest($request);

        if (!$view instanceof TemplateInterface) {
            if (
                AcceptHeader::fromString($request->headers->get('Accept'))->has('application/json') &&
                $view instanceof \ArrayAccess
            ) {
                $event->setResponse(new JsonResponse($view));
            }
            return;
        }

        $event->setResponse($inertia->render(
            $view->getTemplate(),
            $view->parameters,
            $view->code,
            $view->headers->all()
        ));

//        $event->setResponse(new Response(
//            $this->getContainer()->get(Engine::class)->renderToString($view->getTemplate(), $view->parameters),
//            $view->code,
//            $view->headers->all()
//        ));
    }
}
