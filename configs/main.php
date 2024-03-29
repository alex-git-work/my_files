<?php

return [
    'app_deployed' => params('app_deployed', true),
    'main_url' => params('main_url', ''),
    'debug_mode' => params('debug_mode', false),
    'show_queries' => params('show_queries', false),
    'encoding' => params('encoding', 'UTF_8'),
    'token_ttl' => params('token_ttl', 20),
    'email' => params('email', ''),
    'max_file_size' => params('max_file_size', 104857600),
];
