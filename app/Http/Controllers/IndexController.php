<?php

namespace App\Http\Controllers;

use App\Templates\HomeTemplate;
use Framewire\Foundation\Views\Template;
use Symfony\Component\Console\Application;

class IndexController extends Controller
{
    public function home(): Template
    {
        dd($this->getContainer()->get(Application::class));
        return new HomeTemplate();
    }
}
