<?php

namespace App\Templates;

use Framewire\Foundation\Views\Template;

class HomeTemplate extends Template
{
    public function __construct()
    {
        parent::__construct('home.latte');
    }
}
