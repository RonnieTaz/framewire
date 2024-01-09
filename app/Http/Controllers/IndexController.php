<?php

namespace App\Http\Controllers;

use App\Templates\HomeTemplate;
use Framewire\Foundation\Views\Template;

class IndexController extends Controller
{
    public function home(): Template
    {
        return new HomeTemplate();
    }
}
