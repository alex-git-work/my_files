<?php

use App\Controllers\AdminController;
use App\Controllers\DirectoryController;
use App\Controllers\FileController;
use App\Controllers\UserController;

return [
    '/user/*' => [UserController::class, 'edit'],
    '/admin/user/*' => [AdminController::class, 'edit'],
    '/file/*' => [FileController::class, 'edit'],
    '/directory/*' => [DirectoryController::class, 'edit'],
];
