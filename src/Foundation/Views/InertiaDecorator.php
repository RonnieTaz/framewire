<?php

namespace Framewire\Foundation\Views;

use Framewire\Inertia\Contracts\InertiaViewProviderInterface;
use Framewire\Inertia\Entities\Page;
use Latte\Engine;

readonly class InertiaDecorator implements InertiaViewProviderInterface
{
    public function __construct(private Engine $engine)
    {
    }

    public function __invoke(Page $page): string
    {
        return $this->engine->renderToString($page->getComponent(), $page->getProps());
    }
}
