<?php

namespace Framewire\Providers;

use Framewire\Enum\Filesystem\Adapter;
use Framewire\Enum\Filesystem\Driver;
use League\Container\Argument\ResolvableArgument;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

class FilesystemServiceProvider extends AbstractServiceProvider
{

    public function provides(string $id): bool
    {
        return in_array($id, [
            Driver::LOCAL->value,
            Driver::PUBLIC->value
        ]);
    }

    public function register(): void
    {
        $this->getContainer()->add(Adapter::PUBLIC->value, LocalFilesystemAdapter::class)
            ->addArgument(dirname(__DIR__, 2) . '/public');
        $this->getContainer()->add(Adapter::LOCAL->value, LocalFilesystemAdapter::class)
            ->addArgument(dirname(__DIR__, 2) . '/storage');
        $this->getContainer()->add(Driver::PUBLIC->value, Filesystem::class)
            ->addArgument(new ResolvableArgument(Adapter::PUBLIC->value));
        $this->getContainer()->add(Driver::LOCAL->value, Filesystem::class)
            ->addArgument(new ResolvableArgument(Adapter::LOCAL->value));
    }
}
