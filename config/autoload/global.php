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
            'Zend\Cache\Storage\Filesystem' => function($sm){
                $cache = Zend\Cache\StorageFactory::factory(array(
                    'adapter' => 'memory',
                    'plugins' => array(
                        'exception_handler' => array('throw_exceptions' => false),
                        'serializer'
                    )
                ));

                $cache->setOptions(array(
//                    'cache_dir' => './data/cache'
                ));

                return $cache;
            },
            'AdapterFirst' => 'Zend\Db\Adapter\AdapterServiceFactory',
            'admin_menu' => 'Admin\Model\SecondaryNavigationFactory',
			'Adapter' => function ($sm) {
				$config = $sm->get('Config');
				return new Zend\Db\Adapter\Adapter($config['db']);
			},
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
    ),
    'session' => array(
        'cookie_lifetime' => 2419200, //SEE ME
        'remember_me_seconds' => 2419200, //SEE ME
        'use_cookies' => true,
        'cookie_httponly' => true,
    )
);
