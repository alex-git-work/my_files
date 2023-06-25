<?php

use App\Controllers\AdminController;
use App\Controllers\MainController;
use App\Controllers\UserController;

return [
    '/' => [MainController::class, 'index'],
    '/user' => [UserController::class, 'index'],
    '/users/*' => [UserController::class, 'show'],
    '/user/login' => [UserController::class, 'login'],
    '/user/logout' => [UserController::class, 'logout'],
    '/user/reset_password' => [UserController::class, 'resetPassword'],
    '/user/password_change/*' => [UserController::class, 'passwordChange'],
    '/admin/user' => [AdminController::class, 'index'],
    '/admin/users/*' => [AdminController::class, 'show'],
];
