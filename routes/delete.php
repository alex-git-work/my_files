<?php

use App\Controllers\UserController;

return [
    '/user/*' => [UserController::class, 'destroy'],
];
