<?php

return [
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'rukometni_centar',
        'username' => 'root',
        'password' => 'your_local_password',
        'charset' => 'utf8mb4',
    ],
    'jwt' => [
        'secret' => 'replace_this_with_a_long_secure_secret',
        'expiration' => 3600,
    ],
];
