<?php

namespace Framewire\Enum;

enum Logger: string
{
    case EVENT = 'event';
    case HTTP = 'http';
    case DB = 'db';
    case VIEW = 'view';
}
