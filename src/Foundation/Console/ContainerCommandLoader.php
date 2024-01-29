<?php

namespace Framewire\Foundation\Console;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;

class ContainerCommandLoader implements CommandLoaderInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    private array $map;

    // TODO: Implement Command discovery and caching

    /**
     * @inheritDoc
     * @param string $name
     * @return Command
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function get(string $name): Command
    {
        return $this->getContainer()->get($this->map[$name]);
    }

    /**
     * @inheritDoc
     */
    public function has(string $name): bool
    {
        return $this->getContainer()->has($this->map[$name]);
    }

    /**
     * @inheritDoc
     */
    public function getNames(): array
    {
        return array_unique(
            array_map(function (Command $class) {
                $this->map[$class->getName()] = $class::class;
                return $class->getName();
            }, $this->getContainer()->get('commands'))
        );
    }
}
