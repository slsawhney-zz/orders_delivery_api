<?php
return [
    'settings' => [
        'displayErrorDetails' => ('true' === getenv('DEBUG_DETAIL')), // set to false in production
        'service_directories' => [
            'services'    => APP_ROOT.'/configuration/services/',
            'middlewares' => APP_ROOT.'/configuration/middleware/',
            'routes'      => APP_ROOT.'/configuration/routes/',
        ], 

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
        ],

	'db' => [
            'driver' => 'mysql',
            'host' => 'mysql',
            'database' => 'deliveryorders',
            'username' => 'root',
            'password' => 'password',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]
    ],
];
