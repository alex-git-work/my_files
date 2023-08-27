<?php

use App\Controllers\DirectoryController;
use App\Controllers\FileController;
use App\Controllers\UserController;

return [
    '/user' => [UserController::class, 'create'],
    '/file' => [FileController::class, 'create'],
    '/file/download/*' => [FileController::class, 'download'],
    '/directory' => [DirectoryController::class, 'create'],
];
