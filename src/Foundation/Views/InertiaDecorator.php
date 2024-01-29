<?php

namespace Framewire\Foundation\Views;

use Framewire\Inertia\Contracts\InertiaViewProviderInterface;
use Framewire\Inertia\Entities\Page;
use Latte\Engine;
use Latte\Runtime\Html;

readonly class InertiaDecorator implements InertiaViewProviderInterface
{
    public function __construct(private Engine $engine)
    {
    }

    public function __invoke(Page $page): string
    {
        return $this->engine->renderToString('app.latte', ['props' => new Html(json_encode($page->jsonSerialize()))]);
    }
}
