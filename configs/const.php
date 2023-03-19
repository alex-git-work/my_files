<?php

const IS_DEV_SERVER = true;

if (!defined('ABSPATH')) {
    define('ABSPATH', $_SERVER['DOCUMENT_ROOT'] . '/');
}

const UPLOADS = ABSPATH . 'uploads/';

const CONFIGS = ABSPATH . 'configs/';

const ROUTES = ABSPATH . 'routes/';

const ENV = CONFIGS . 'env/';
