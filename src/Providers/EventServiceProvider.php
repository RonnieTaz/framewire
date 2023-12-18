<?php

declare(strict_types=1);

namespace Framewire\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Event\EventDispatcher;

class EventServiceProvider extends AbstractServiceProvider
{

    public function provides(string $id): bool
    {
        return in_array($id, [
            EventDispatcher::class
        ]);
    }

    public function register(): void
    {
        $this->getContainer()->add(EventDispatcher::class);
    }
}
