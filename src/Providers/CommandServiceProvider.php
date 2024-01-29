<?php

namespace Framewire\Providers;

use Framewire\Foundation\Console\ContainerCommandLoader;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyDispatcher;


class CommandServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{

    public function provides(string $id): bool
    {
        return in_array($id, [
            Application::class,
            ContainerCommandLoader::class
        ]);
    }

    public function register(): void
    {
        $this->getContainer()->add(ContainerCommandLoader::class);
        $this->getContainer()->add(Application::class)
            ->addMethodCall('setDispatcher', [SymfonyDispatcher::class]);
    }

    public function boot(): void
    {
        $this->getContainer()->inflector(Application::class)
            ->invokeMethod('setCommandLoader', [ContainerCommandLoader::class]);
    }
}
