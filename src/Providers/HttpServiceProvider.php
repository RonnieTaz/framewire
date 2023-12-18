<?php

declare(strict_types=1);

namespace Framewire\Providers;

use League\Container\Argument\ArgumentResolverInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Event\EventDispatcher;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\HttpKernel;

class HttpServiceProvider extends AbstractServiceProvider
{

    public function provides(string $id): bool
    {
        return in_array($id, [
            HttpKernel::class,
            Request::class,
            RequestStack::class,
            ControllerResolverInterface::class,
            ArgumentResolverInterface::class
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function register(): void
    {
        $this->getContainer()->add(Request::class, Request::createFromGlobals());
        $this->getContainer()->add(HttpKernel::class)->addArguments([
            $this->getContainer()->get(EventDispatcher::class),
            $this->getContainer()->get(ControllerResolverInterface::class),
            $this->getContainer()->get(RequestStack::class),
            $this->getContainer()->get(ArgumentResolverInterface::class)
        ]);
    }
}
