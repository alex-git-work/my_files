<?php

use App\Controllers\AdminController;
use App\Controllers\DirectoryController;
use App\Controllers\FileController;
use App\Controllers\UserController;

return [
    '/user/*' => [UserController::class, 'destroy'],
    '/admin/user/*' => [AdminController::class, 'destroy'],
    '/file/*' => [FileController::class, 'destroy'],
    '/files/share/*/*' => [FileController::class, 'stopUserAccess'],
    '/directory/*' => [DirectoryController::class, 'destroy'],
];
