<?php

namespace Framewire\Foundation;

use App\Http\Controllers\Controller;
use Aura\Router\Exception\RouteAlreadyExists;
use Aura\Router\RouterContainer;
use Crell\Tukio\ProviderBuilder;
use Crell\Tukio\ProviderCompiler;
use Exception;
use Framewire\Contracts\Event\EventListenerInterface;
use Framewire\Enum\ApplicationMode;
use Framewire\Foundation\Events\EventProvider;
use Framewire\Foundation\Events\Listeners\Http\ControllerResponseListener;
use Framewire\Foundation\Events\Listeners\Http\ExceptionListener;
use Framewire\Foundation\Events\Listeners\Http\RouteListener;
use Framewire\Providers\EventServiceProvider;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernel;

class App implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function bootstrap(): static
    {
        $this->loadEventListeners(find_classes(dirname(__FILE__) . '/Events/Listeners'));
        $this->loadControllers(find_classes(dirname(__DIR__, 2) . '/app/Http/Controllers'));
        $this->loadCommands(find_classes(
            dirname(__DIR__, 2) . '/app/Console/Commands',
            dirname(__FILE__) . '/Console/Commands'
        ));

        $routes = include dirname(__DIR__, 2) . '/routes/web.php';

        try {
            $routes($this->getContainer()->get(RouterContainer::class)->getMap());
        } catch (RouteAlreadyExists) {
//            TODO: Optimize routing to use facade-like implementation
        }
        return $this;
    }

    /**
     * This method is used in the EventServiceProvider to register the Event Provider
     * @see EventServiceProvider::register()
     * @return EventProvider
     */
    public function registerEvents(): EventProvider
    {
        $this->compileEvents();

        return new EventProvider($this->getContainer());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function run(ApplicationMode $mode = ApplicationMode::HTTP): void
    {
        switch ($mode) {
            case ApplicationMode::HTTP:
                /**
                 * @var $kernel HttpKernel
                 */
                $kernel = $this->getContainer()->get(HttpKernel::class);
                /**
                 * @var $request Request
                 */
                $request = $this->getContainer()->get(Request::class);

                $response = $kernel->handle($request);
                $response->send();

                $kernel->terminate($request, $response);
                break;
            case ApplicationMode::CLI:
                /**
                 * @var $kernel Application
                 */
                $kernel = $this->getContainer()->get(Application::class);

                $kernel->run();
        }
    }

    private function compileEvents(): void
    {
        $builder = new ProviderBuilder();

        $builder->addListenerService(RouteListener::class, 'handle', RequestEvent::class);
        $builder->addListenerService(ExceptionListener::class, 'handle', ExceptionEvent::class, 32);
        $builder->addListenerService(ControllerResponseListener::class, 'handle', ViewEvent::class, 30);

        $compiler = new ProviderCompiler();

        $f = fopen(__DIR__ . '/Events/EventProvider.php', 'w');

        $compiler->compile($builder, $f, 'EventProvider', 'Framewire\\Foundation\\Events');

        fclose($f);
    }

    private function loadEventListeners(array $listeners): void
    {
        array_walk($listeners, function (ReflectionClass $class) {
            if (is_subclass_of($class->getName(), EventListenerInterface::class)) {
                $this->getContainer()->add($class->getName());
            }
        });
    }

    private function loadControllers(array $controllers): void
    {
        array_walk($controllers, function (ReflectionClass $class) {
            if ($class->getName() !== Controller::class && is_subclass_of($class->getName(), Controller::class)) {
                $this->getContainer()->add($class->getName());
            }
        });
    }

    private function loadCommands(array $commands): void
    {
        array_walk($commands, function (ReflectionClass $class) {
            if (is_subclass_of($class->getName(), Command::class)) {
                $this->getContainer()->add($class->getName())->addTag('commands');
            }
        });
    }
}
