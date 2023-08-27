<?php

use App\Controllers\AdminController;
use App\Controllers\DirectoryController;
use App\Controllers\FileController;
use App\Controllers\UserController;

return [
    '/user/*' => [UserController::class, 'destroy'],
    '/admin/user/*' => [AdminController::class, 'destroy'],
    '/file/*' => [FileController::class, 'destroy'],
    '/directory/*' => [DirectoryController::class, 'destroy'],
];
