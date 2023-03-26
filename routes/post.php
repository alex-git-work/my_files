<?php

use App\Controllers\MainController;

return [
    '/update' => [MainController::class, 'update'],
    '/users' => [MainController::class, 'users'],
];
