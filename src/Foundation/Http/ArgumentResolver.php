<?php

namespace Framewire\Foundation\Http;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;

class ArgumentResolver implements ArgumentResolverInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @inheritDoc
     */
    public function getArguments(Request $request, callable $controller): array
    {
        return [];
    }
}
