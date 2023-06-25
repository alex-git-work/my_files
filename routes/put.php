<?php

use App\Controllers\AdminController;
use App\Controllers\UserController;

return [
    '/user/*' => [UserController::class, 'edit'],
    '/admin/user/*' => [AdminController::class, 'edit'],
];
