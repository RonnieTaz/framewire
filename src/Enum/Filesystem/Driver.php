<?php

namespace Framewire\Enum\Filesystem;

enum Driver: string
{
    case PUBLIC = 'filesystem.public';
    case LOCAL = 'filesystem.local';
}
