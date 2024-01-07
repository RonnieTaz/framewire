<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
    public function home(): Response
    {
        return (new Response('Hello World'));
    }
}
