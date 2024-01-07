<?php

namespace Framewire\Foundation\Events\Subscribers;

use Crell\Tukio\ListenerProxy;
use Crell\Tukio\SubscriberInterface;
use Framewire\Foundation\Events\Listeners\RouteListener;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class KernelRequestSubscriber implements SubscriberInterface
{

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function onKernelEvent(RequestEvent $event): void
    {
        (new RouteListener())($event);
    }

    public static function registerListeners(ListenerProxy $proxy): void
    {
        $id = $proxy->addListener();
    }
}
