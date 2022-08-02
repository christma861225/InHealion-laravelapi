<?php


return [

    'driver' => env('MAIL_DRIVER', 'smtp'),
    'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
    'port' => env('MAIL_PORT', 587),

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'daniil@inhealion.gr'),
        'name' => env('MAIL_FROM_NAME', 'Support team'),
    ],

    'encryption' => env('MAIL_ENCRYPTION', 'tls'),

    'username' => env('MAIL_USERNAME'),
    'password' => env('MAIL_PASSWORD'),

    'sendmail' => '/usr/bin/mhsendmail --smtp-addr mailhog:1025',

    'markdown' => [
        'theme' => 'default',
        'paths' => [
            resource_path('views/mail'),
        ],
    ],
];
