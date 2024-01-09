<?php

namespace Framewire\Foundation\Views;

use Framewire\Contracts\View\TemplateInterface;
use Framewire\Contracts\View\TemplateParameterInterface;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Response;

abstract class Template implements TemplateInterface
{
    public function __construct(
        public string $template,
        public array|TemplateParameterInterface $parameters = [],
        public int $code = Response::HTTP_OK,
        public HeaderBag $headers = new HeaderBag()
    )
    {
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param HeaderBag $headers
     * @return Template
     */
    public function setHeaders(HeaderBag $headers): static
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @param int $code
     * @return Template
     */
    public function setCode(int $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @param TemplateParameterInterface|null $parameters
     * @return Template
     */
    public function setParameters(?TemplateParameterInterface $parameters): static
    {
        $this->parameters = $parameters;

        return $this;
    }
}
