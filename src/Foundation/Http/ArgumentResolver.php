<?php

namespace Framewire\Foundation\Http;

use InvalidArgumentException;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use League\Container\ServiceProvider\ServiceProviderInterface;
use LogicException;
use Psr\Container\ContainerExceptionInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\DefaultValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestAttributeValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\SessionValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\VariadicValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactory;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactoryInterface;
use Symfony\Component\HttpKernel\Exception\ResolverNotFoundException;

class ArgumentResolver implements ArgumentResolverInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function __construct(
        private readonly ArgumentMetadataFactoryInterface $argumentMetadataFactory = new ArgumentMetadataFactory(),
        private iterable $argumentValueResolvers = []
    )
    {
        $this->argumentValueResolvers = $argumentValueResolvers ?: self::getDefaultArgumentValueResolvers();
    }

    /**
     * @inheritDoc
     * @throws ContainerExceptionInterface
     */
    public function getArguments(Request $request, callable $controller): array
    {
        $arguments = [];

        foreach ($this->argumentMetadataFactory->createArgumentMetadata($controller) as $metadata) {
            $argumentValueResolvers = $this->argumentValueResolvers;
            $disabledResolvers = [];

            if ($this->getContainer() && $attributes = $metadata->getAttributesOfType(ValueResolver::class, $metadata::IS_INSTANCEOF)) {
                $resolverName = null;
                foreach ($attributes as $attribute) {
                    if ($attribute->disabled) {
                        $disabledResolvers[$attribute->resolver] = true;
                    } elseif ($resolverName) {
                        throw new LogicException(sprintf('You can only pin one resolver per argument, but argument "$%s" of "%s()" has more.', $metadata->getName(), $this->getPrettyName($controller)));
                    } else {
                        $resolverName = $attribute->resolver;
                    }
                }

                if ($resolverName) {
                    if (!$this->getContainer()->has($resolverName)) {
                        throw new ResolverNotFoundException($resolverName, $this->getContainer() instanceof ServiceProviderInterface ? array_keys($this->getContainer()->getProvidedServices()) : []);
                    }

                    $argumentValueResolvers = [
                        $this->getContainer()->get($resolverName),
                        new RequestAttributeValueResolver(),
                        new DefaultValueResolver(),
                    ];
                }
            }

            foreach ($argumentValueResolvers as $name => $resolver) {
                if (isset($disabledResolvers[is_int($name) ? $resolver::class : $name])) {
                    continue;
                }

                $count = 0;
                foreach ($resolver->resolve($request, $metadata) as $argument) {
                    ++$count;
                    $arguments[] = $argument;
                }

                if (1 < $count && !$metadata->isVariadic()) {
                    throw new InvalidArgumentException(sprintf('"%s::resolve()" must yield at most one value for non-variadic arguments.', get_debug_type($resolver)));
                }

                if ($count) {
                    // continue to the next controller argument
                    continue 2;
                }
            }

            throw new RuntimeException(sprintf('Controller "%s" requires that you provide a value for the "$%s" argument. Either the argument is nullable and no null value has been provided, no default value has been provided or there is a non-optional argument after this one.', $this->getPrettyName($controller), $metadata->getName()));
        }

        return $arguments;
    }

    /**
     * @return iterable<int, ValueResolverInterface>
     */
    public static function getDefaultArgumentValueResolvers(): iterable
    {
        return [
            new RequestAttributeValueResolver(),
            new RequestValueResolver(),
            new SessionValueResolver(),
            new DefaultValueResolver(),
            new VariadicValueResolver(),
        ];
    }

    private function getPrettyName($controller): string
    {
        if (is_array($controller)) {
            if (is_object($controller[0])) {
                $controller[0] = get_debug_type($controller[0]);
            }

            return $controller[0].'::'.$controller[1];
        }

        if (is_object($controller)) {
            return get_debug_type($controller);
        }

        return $controller;
    }
}
