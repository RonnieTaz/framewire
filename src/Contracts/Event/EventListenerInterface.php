<?php

namespace Framewire\Contracts\Event;

use Psr\EventDispatcher\StoppableEventInterface;

interface EventListenerInterface
{
    public function handle(StoppableEventInterface $event): void;
}
