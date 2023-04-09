<?php

return [
    'db_user' => params('db_user'),
    'db_password' => params('db_password'),

    'dsn' => 'mysql:host=' . params('db_host') . ';port=' . params('db_port') . ';dbname=' . params('db_name') . ';charset=' . params('db_charset'),

    'options' => [
        PDO::MYSQL_ATTR_FOUND_ROWS => params('pdo_found_rows'),
        PDO::ATTR_ERRMODE => params('pdo_errmode'),
        PDO::ATTR_DEFAULT_FETCH_MODE => params('pdo_fetch_mode'),
    ],
];
