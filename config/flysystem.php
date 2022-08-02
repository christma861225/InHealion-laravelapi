<?php

return [
    'default' => 'local',

    'connections' => [
        'local' => [
            'driver' => 'local',
            'path' => storage_path('app'),
            'permissions' => [
                'file' => [
                    'public' => 0664,
                    'private' => 0600,
                ],
                'dir' => [
                    'public' => 0775,
                    'private' => 0700,
                ],
            ],
            // 'visibility' => 'public',
            // 'pirate'     => false,
            // 'eventable'  => true,
            // 'cache'      => 'foo'
        ],

        'null' => [
            'driver' => 'null',
            // 'eventable' => true,
            // 'cache'     => 'foo'
        ],
        'zip' => [
            'driver' => 'zip',
            'path' => storage_path('files.zip'),
            // 'visibility' => 'public',
            // 'pirate'     => false,
            // 'eventable'  => true,
            // 'cache'      => 'foo'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Flysystem Cache
    |--------------------------------------------------------------------------
    |
    | Here are each of the cache configurations setup for your application.
    | There are currently two drivers: illuminate and adapter. Examples of
    | configuration are included. You can of course have multiple connections
    | per driver as shown.
    |
    */

    'cache' => [
        'adapter' => [
            'driver' => 'adapter',
            'adapter' => 'local', // as defined in connections
            'file' => 'flysystem.json',
            'ttl' => 600,
        ],
    ],
];
