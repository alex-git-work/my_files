<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Main application settings
    |--------------------------------------------------------------------------
    |
    | All settings in this section will be available through the App::$params
    |
    */

    'main_url' => 'http://files.lcl',
    'debug_mode' => true,
    'show_queries' => true,
    'encoding' => 'UTF-8',
    'token_ttl' => 10,
    'email' => 'info@files.lcl',
    'max_file_size' => 2147483648,


    /*
    |--------------------------------------------------------------------------
    | MySQL server settings
    |--------------------------------------------------------------------------
    |
    | You can connect to the MySQL server using following settings.
    |
    */

    'db_user' => 'mysql',
    'db_password' => 'mysql',
    'db_host' => 'localhost',
    'db_port' => 3306,
    'db_name' => 'drive',
    'db_charset' => 'utf8',


    /*
    |--------------------------------------------------------------------------
    | Default PDO settings
    |--------------------------------------------------------------------------
    |
    | 1. Return the number of found (matched) rows, not the number of changed rows.
    | 2. Throw a PDOException if an error occurs.
    | 3. Specifies that the fetch method shall return each row as an array indexed by column name as returned in the corresponding result set.
    | If the result set contains multiple columns with the same name, PDO::FETCH_ASSOC returns only a single value per column name.
    |
    */

    'pdo_found_rows' => true,
    'pdo_errmode' => PDO::ERRMODE_EXCEPTION,
    'pdo_fetch_mode' => PDO::FETCH_ASSOC,
];
