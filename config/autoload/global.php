<?php

return array(
    'db' => array(
        'driver'         => 'Pdo',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),

    'service_manager' => array(
        'factories' => array(
            'AdapterFirst' => 'Zend\Db\Adapter\AdapterServiceFactory',
            'admin_menu' => 'Admin\Model\SecondaryNavigationFactory',
			'Adapter' => function ($sm) {
				$config = $sm->get('Config');
				return new Zend\Db\Adapter\Adapter($config['db']);
			},
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => false,
        'display_exceptions'       => false,
    ),
    'session' => array(
        'cookie_lifetime' => 2419200, //SEE ME
        'remember_me_seconds' => 2419200, //SEE ME
        'use_cookies' => true,
        'cookie_httponly' => true,
    )
);
