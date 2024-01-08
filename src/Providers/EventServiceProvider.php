<?php

declare(strict_types=1);

namespace Framewire\Providers;

use Crell\Tukio\CompiledListenerProviderBase;
use Crell\Tukio\Dispatcher;
use Framewire\Adapters\Event\EventDispatcherAdapter;
use Framewire\Enum\Logger;
use Framewire\Foundation\App;
use Framewire\Foundation\Logs\Loggers\AggregateLogger;
use League\Container\ServiceProvider\AbstractServiceProvider;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyDispatcher;

class EventServiceProvider extends AbstractServiceProvider
{

    public function provides(string $id): bool
    {
        return in_array($id, [
            CompiledListenerProviderBase::class,
            EventDispatcherInterface::class,
            SymfonyDispatcher::class,
        ]);
    }

    public function register(): void
    {
        $this->getContainer()->add(CompiledListenerProviderBase::class, $this->getContainer()->get(App::class)->registerEvents());
        $this->getContainer()->add(EventDispatcherInterface::class, Dispatcher::class)
            ->addArgument(CompiledListenerProviderBase::class)
            ->addArgument($this->getContainer()->get(AggregateLogger::class)->getLogger(Logger::EVENT))
            ->setShared();
        $this->getContainer()->add(SymfonyDispatcher::class, EventDispatcherAdapter::class)
            ->addArgument(EventDispatcherInterface::class)
            ->setShared();
    }
}
