<?php

namespace Framewire\Providers;

use Latte\Engine;
use Latte\Loaders\FileLoader;
use League\Container\ServiceProvider\AbstractServiceProvider;

class TemplateServiceProvider extends AbstractServiceProvider
{

    public function provides(string $id): bool
    {
        return in_array($id, [
            Engine::class
        ]);
    }

    public function register(): void
    {
        $this->getContainer()->add(FileLoader::class)->addArgument(dirname(__DIR__, 2) . '/resources/views');
        $this->getContainer()->add(Engine::class)
            ->addMethodCall('setLoader', [FileLoader::class])
            ->addMethodCall('setTempDirectory', [dirname(__DIR__, 2) . '/storage/cache/views'])
            ->addMethodCall('setStrictTypes')
            ->addMethodCall('setStrictParsing')
            ->setShared();
    }
}
