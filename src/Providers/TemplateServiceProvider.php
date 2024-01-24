<?php

namespace Framewire\Providers;

use Framewire\Foundation\Views\InertiaDecorator;
use Framewire\Inertia\Contracts\InertiaInterface;
use Framewire\Inertia\Contracts\InertiaViewProviderInterface;
use Framewire\Inertia\Core\Inertia;
use Latte\Engine;
use Latte\Loaders\FileLoader;
use Latte\Runtime\Template;
use League\Container\ServiceProvider\AbstractServiceProvider;

class TemplateServiceProvider extends AbstractServiceProvider
{

    public function provides(string $id): bool
    {
        return in_array($id, [
            Engine::class,
            InertiaInterface::class
        ]);
    }

    public function register(): void
    {
        $this->getContainer()->add(FileLoader::class)->addArgument(dirname(__DIR__, 2) . '/resources/views');
        $this->getContainer()->add(InertiaInterface::class, Inertia::class)->addArguments([
            null,
            null,
            $this->getContainer()
        ]);
        $this->getContainer()->add(InertiaViewProviderInterface::class, InertiaDecorator::class)
            ->addArgument(Engine::class);
        $this->getContainer()->add(Engine::class)
            ->addMethodCall('setLoader', [FileLoader::class])
            ->addMethodCall('setTempDirectory', [dirname(__DIR__, 2) . '/storage/cache/views'])
            ->addMethodCall('setStrictTypes')
            ->addMethodCall('setStrictParsing')
            ->addMethodCall('addProvider', [
                'coreParentFinder',
                function (Template $template) {
                    if (!$template->getReferenceType()) {
                        // it returns the path to the parent template file
                        return 'layouts/base.latte';
                    }
                }
            ])
            ->setShared();
    }
}
