<?php

namespace Framewire\Foundation\Views;

use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Response;

abstract class InertiaTemplate extends Template
{
    public function __construct(string $template, array $parameters = [], int $code = Response::HTTP_OK, HeaderBag $headers = new HeaderBag())
    {
        parent::__construct($template, $parameters, $code, new HeaderBag(['X-Inertia' => true, ...$headers->all()]));
    }
}
