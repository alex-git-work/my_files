<?php

use App\Controllers\MainController;

return [
    '/' => [MainController::class, 'index'],
    '/user/*' => [MainController::class, 'user'],
];
