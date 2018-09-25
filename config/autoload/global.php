<?php

return [
    'db' => [
        'driver'         => 'Pdo',
        'driver_options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
    ],
    'service_manager' => [
        'factories' => [
            'AdapterFirst' => 'Zend\Db\Adapter\AdapterServiceFactory',
            'admin_menu' => 'Admin\Model\SecondaryNavigationFactory',
			'Adapter' => function ($sm) {
				$config = $sm->get('Config');
				return new Zend\Db\Adapter\Adapter($config['db']);
			},
        ],
    ],
    'session' => [
        'cookie_lifetime' => 2419200, //SEE ME
        'remember_me_seconds' => 2419200, //SEE ME
        'use_cookies' => true,
		'gc_maxlifetime' => 2419200,
		'name' => 'zf3',
    ]
];
