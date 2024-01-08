<?php

namespace Framewire\Enum\Filesystem;

enum Adapter: string
{
    case PUBLIC = 'filesystem.adapter.public';
    case LOCAL = 'filesystem.adapter.local';
}
