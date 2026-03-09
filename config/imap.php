<?php

return [
    'default' => [
        'host'       => 'imap.gmail.com',
        'port'       => 993,
        'encryption' => 'ssl',
        'validate_cert' => true,
        'username'   => env('IMAP_USERNAME'),
        'password'   => env('IMAP_PASSWORD'),
    ],
];
