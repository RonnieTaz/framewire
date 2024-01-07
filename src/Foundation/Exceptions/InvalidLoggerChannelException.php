<?php

namespace Framewire\Foundation\Exceptions;

class InvalidLoggerChannelException extends \Exception
{
    public static function reason(string $message): static
    {
        return new static($message);
    }
}
