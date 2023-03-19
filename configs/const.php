<?php

const IS_DEV_SERVER = false;

if (!defined('ABSPATH')) {
    define('ABSPATH', $_SERVER['DOCUMENT_ROOT'] . '/');
}

const UPLOADS = ABSPATH . 'uploads/';

const CONFIGS = ABSPATH . 'configs/';

const ROUTES = ABSPATH . 'routes/';
