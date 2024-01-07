<?php

namespace Framewire\Foundation\Http;

use Closure;
use Error;
use InvalidArgumentException;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use LogicException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

class ControllerResolver implements ControllerResolverInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @inheritDoc
     * @throws ContainerExceptionInterface|ReflectionException
     */
    public function getController(Request $request): callable|false
    {
        if (!$controller = $request->attributes->get('_controller')) {
            return false;
        }

        if (is_array($controller)) {
            if (isset($controller[0]) && is_string($controller[0]) && isset($controller[1])) {
                try {
                    $controller[0] = $this->instantiateController($controller[0]);
                } catch (Error|LogicException $e) {
                    if (is_callable($controller)) {
                        return $this->checkController($controller);
                    }

                    throw $e;
                }
            }

            if (!is_callable($controller)) {
                throw new InvalidArgumentException(sprintf('The controller for URI "%s" is not callable: ', $request->getPathInfo()).$this->getControllerError($controller));
            }

            return $this->checkController($controller);
        }

        if (is_object($controller)) {
            if (!is_callable($controller)) {
                throw new InvalidArgumentException(sprintf('The controller for URI "%s" is not callable: ', $request->getPathInfo()).$this->getControllerError($controller));
            }

            return $this->checkController($controller);
        }

        if (function_exists($controller)) {
            return $this->checkController($controller);
        }

        try {
            $callable = $this->createController($controller);
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException(sprintf('The controller for URI "%s" is not callable: ', $request->getPathInfo()).$e->getMessage(), 0, $e);
        }

        if (!is_callable($callable)) {
            throw new InvalidArgumentException(sprintf('The controller for URI "%s" is not callable: ', $request->getPathInfo()).$this->getControllerError($callable));
        }

        return $this->checkController($callable);
    }

    /**
     * Returns a callable for the given controller.
     *
     * @throws InvalidArgumentException|ContainerExceptionInterface When the controller cannot be created
     */
    protected function createController(string $controller): callable
    {
        if (!str_contains($controller, '::')) {
            $controller = $this->instantiateController($controller);

            if (!is_callable($controller)) {
                throw new InvalidArgumentException($this->getControllerError($controller));
            }

            return $controller;
        }

        [$class, $method] = explode('::', $controller, 2);

        try {
            $controller = [$this->instantiateController($class), $method];
        } catch (Error|LogicException $e) {
            try {
                if ((new ReflectionMethod($class, $method))->isStatic()) {
                    return $class.'::'.$method;
                }
            } catch (ReflectionException) {
                throw $e;
            }

            throw $e;
        }

        if (!is_callable($controller)) {
            throw new InvalidArgumentException($this->getControllerError($controller));
        }

        return $controller;
    }

    /**
     * Returns an instantiated controller.
     * @param string $class
     * @return object
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function instantiateController(string $class): object
    {
        return $this->getContainer()->get($class);
    }

    private function getClassMethodsWithoutMagicMethods($classOrObject): array
    {
        $methods = get_class_methods($classOrObject);

        return array_filter($methods, fn (string $method) => 0 !== strncmp($method, '__', 2));
    }

    private function getControllerError(mixed $callable): string
    {
        if (is_string($callable)) {
            if (str_contains($callable, '::')) {
                $callable = explode('::', $callable, 2);
            } else {
                return sprintf('Function "%s" does not exist.', $callable);
            }
        }

        if (is_object($callable)) {
            $availableMethods = $this->getClassMethodsWithoutMagicMethods($callable);
            $alternativeMsg = $availableMethods ? sprintf(' or use one of the available methods: "%s"', implode('", "', $availableMethods)) : '';

            return sprintf('Controller class "%s" cannot be called without a method name. You need to implement "__invoke"%s.', get_debug_type($callable), $alternativeMsg);
        }

        if (!is_array($callable)) {
            return sprintf('Invalid type for controller given, expected string, array or object, got "%s".', get_debug_type($callable));
        }

        if (!isset($callable[0]) || !isset($callable[1]) || 2 !== count($callable)) {
            return 'Invalid array callable, expected [controller, method].';
        }

        [$controller, $method] = $callable;

        if (is_string($controller) && !class_exists($controller)) {
            return sprintf('Class "%s" does not exist.', $controller);
        }

        $className = is_object($controller) ? get_debug_type($controller) : $controller;

        if (method_exists($controller, $method)) {
            return sprintf('Method "%s" on class "%s" should be public and non-abstract.', $method, $className);
        }

        $collection = $this->getClassMethodsWithoutMagicMethods($controller);

        $alternatives = [];

        foreach ($collection as $item) {
            $lev = levenshtein($method, $item);

            if ($lev <= strlen($method) / 3 || str_contains($item, $method)) {
                $alternatives[] = $item;
            }
        }

        asort($alternatives);

        $message = sprintf('Expected method "%s" on class "%s"', $method, $className);

        if (count($alternatives) > 0) {
            $message .= sprintf(', did you mean "%s"?', implode('", "', $alternatives));
        } else {
            $message .= sprintf('. Available methods: "%s".', implode('", "', $collection));
        }

        return $message;
    }

    /**
     * @throws ReflectionException
     */
    private function checkController(callable $controller): callable
    {
        if (is_array($controller)) {
            [$class, $name] = $controller;
            $name = (is_string($class) ? $class : $class::class).'::'.$name;
        } elseif (is_object($controller) && !$controller instanceof Closure) {
            $class = $controller;
            $name = $class::class.'::__invoke';
        } else {
            $r = new ReflectionFunction($controller);
            $name = $r->name;

            if (str_contains($name, '{closure}')) {
                $name = $class = \Closure::class;
            } elseif ($class = $r->getClosureCalledClass()) {
                $class = $class->name;
                $name = $class.'::'.$name;
            }
        }

        if ($class) {
            return $controller;
        }

        throw new BadRequestException(sprintf('Callable "%s()" is not allowed as a controller.', $name));
    }
}
