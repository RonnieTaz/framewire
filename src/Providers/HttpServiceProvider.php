<?php

declare(strict_types=1);

namespace Framewire\Providers;

use Aura\Router\RouterContainer;
use Framewire\Enum\Logger;
use Framewire\Foundation\Http\ArgumentResolver;
use Framewire\Foundation\Http\ControllerResolver;
use Framewire\Foundation\Logs\Loggers\AggregateLogger;
use League\Container\Argument\ArgumentResolverInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
            PsrHttpFactory::class,
            Psr17Factory::class,
            ServerRequestInterface::class,
            RequestStack::class,
            ControllerResolverInterface::class,
            ArgumentResolverInterface::class,
            RouterContainer::class
        ]);
    }

    public function register(): void
    {
        $this->getContainer()->add(Request::class, Request::createFromGlobals());
        $this->getContainer()->add(Psr17Factory::class);
        $this->getContainer()->add(PsrHttpFactory::class)->addArguments([
            Psr17Factory::class,
            Psr17Factory::class,
            Psr17Factory::class,
            Psr17Factory::class
        ]);
        $this->getContainer()->add(ServerRequestInterface::class, PsrHttpFactory::class)
            ->addMethodCall('createRequest', [Request::class]);
        $this->getContainer()->add(ControllerResolverInterface::class, new ControllerResolver());
        $this->getContainer()->add(RequestStack::class);
        $this->getContainer()->add(ArgumentResolverInterface::class, new ArgumentResolver());
        $this->getContainer()->add(HttpKernel::class)->addArguments([
            EventDispatcherInterface::class,
            ControllerResolverInterface::class,
            RequestStack::class,
            ArgumentResolverInterface::class
        ]);
        $this->getContainer()->add(RouterContainer::class)
            ->addMethodCall(
                'setLoggerFactory',
                [fn () => $this->getContainer()->get(AggregateLogger::class)->getLogger(Logger::HTTP)]
            )->setShared();
    }
}
