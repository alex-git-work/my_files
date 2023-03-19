<?php

require_once __DIR__ . '/configs/const.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/helpers.php';

$allRoutes = [
    'GET' => include ROUTES . 'get.php',
    'POST' => include ROUTES . 'post.php',
    'PUT' => include ROUTES . 'put.php',
    'DELETE' => include ROUTES . 'delete.php',
];
