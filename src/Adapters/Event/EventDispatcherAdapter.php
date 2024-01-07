<?php

namespace Framewire\Adapters\Event;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

class EventDispatcherAdapter implements SymfonyEventDispatcherInterface
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher
    )
    {
    }

    /**
     * @inheritdoc
     */
    public function dispatch(object $event, string $eventName = null): object
    {
        return $this->dispatcher->dispatch($event);
    }
}
