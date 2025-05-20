<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'apiservices/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['https://frontend-jal-construcciones-ba8c.vercel.app/'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
