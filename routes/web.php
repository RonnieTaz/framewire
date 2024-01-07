<?php

declare(strict_types=1);

use App\Http\Controllers\IndexController;
use Aura\Router\Map;

return function (Map $router) {
    $router->get('welcome', '/', [IndexController::class, 'home']);
};
