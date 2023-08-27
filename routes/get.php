<?php

use App\Base\Deployment;
use App\Controllers\AdminController;
use App\Controllers\DirectoryController;
use App\Controllers\FileController;
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
    '/user/search/*' => [UserController::class, 'search'],
    '/admin/user' => [AdminController::class, 'index'],
    '/admin/users/*' => [AdminController::class, 'show'],
    '/file' => [FileController::class, 'index'],
    '/file/share/*' => [FileController::class, 'sharedList'],
    '/file/shared_for_me' => [FileController::class, 'filesSharedForMe'],
    '/file/my_shared_files' => [FileController::class, 'mySharedFiles'],
    '/files/*' => [FileController::class, 'show'],
    '/directories/*' => [DirectoryController::class, 'show'],
    '/deployment/start' => [Deployment::class, 'start'],
];
