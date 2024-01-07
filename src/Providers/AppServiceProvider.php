<?php

declare(strict_types=1);

namespace Framewire\Providers;

use Framewire\Foundation\App;
use League\Container\ContainerAwareInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

class AppServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{

    public function provides(string $id): bool
    {
        return in_array($id, [
            App::class
        ]);
    }

    public function register(): void
    {
        $this->getContainer()->add(App::class);
    }

    public function boot(): void
    {
        $this->getContainer()->inflector(App::class)->invokeMethod('bootstrap', []);
    }
}
