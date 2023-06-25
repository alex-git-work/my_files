<?php

use App\Controllers\AdminController;
use App\Controllers\UserController;

return [
    '/user/*' => [UserController::class, 'destroy'],
    '/admin/user/*' => [AdminController::class, 'destroy'],
];
