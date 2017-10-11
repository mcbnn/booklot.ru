<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
global $site;
return array(
    'controllers' => array(
        'invokables' => array(
		    'Admin\Controller\Auth' => 'Admin\Controller\AuthController',
        ),
    ),
   'router' => array(
        'routes' => array(
            'admin' => array(
                'type' => 'hostname',
                'options' => array(
                    'route' => 'www.:subdomain.booklot.ru',
                    'constraints' => array(
                        'subdomain' => 'admin'
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Admin',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'slash' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/',
                            'defaults' => array(
                                'controller' => 'Admin\Controller\Admin',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            
                            'changePassword' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => 'changePassword/',
                                    'defaults' => array(
                                        'controller' => 'Admin\Controller\Auth',
                                        'action' => 'changePassword',
                                    ),
                                ),
                            ),
                            
                        ),
                    ),
                ),
            ),
        ),
    ),

    'navigation'=>array(
        'admin_menu'=>array(
            array(
                'label'   => 'Главная страница',
                'route' => 'admin/slash',
                'resource' => 'admin',
                'action' => 'index',
                'params' => array(
                    'subdomain' => 'admin',
                ),
            ),
            array(
                'label' => 'Меню',
                'route' => 'admin/slash/menu',
                'resource' => 'admin',
                'action' => 'index',
                'params' => array(
                    'subdomain' => 'admin',
                ),
                'pages' => array(
                    array(
                        'label' => 'Список',
                        'route' => 'admin/slash/menu',
                        'resource' => 'admin',
                        'action'     => 'index',
                        'params' => array(
                            'subdomain' => 'admin',
                        ),
                    ),
                    array(
                        'label' => 'Список(древовидный)',
                        'route' => 'admin/slash/menu',
                        'resource' => 'admin',
                        'action'     => 'denroidList',
                        'params' => array(
                            'subdomain' => 'admin',
                        ),
                    ),
                    array(
                        'label' => 'Добавить',
                        'route' => 'admin/slash/menu',
                        'resource' => 'admin',
                        'action'     => 'addMenu',
                        'params' => array(
                            'subdomain' => 'admin',
                        ),
                    ),
                    array(
                        'label' => 'Редактировать',
                        'route' => 'admin/slash/menu',
                        'resource' => 'adminnovis',
                        'action'     => 'redactorMenu',
                        'params' => array(
                            'subdomain' => 'admin',
                        ),
                    ),

                ),
            ),
			array(
				'label' => 'Шаблон',
				'route' => 'admin/slash/template',
				'resource' => 'admin',
				'action' => 'index',
				'params' => array(
					'subdomain' => 'admin',
				),
				'pages' => array(
					array(
						'label' => 'Список шаблонов',
						'route' => 'admin/slash/template',
						'resource' => 'admin',
						'action'     => 'index',
						'params' => array(
							'subdomain' => 'admin',
						),
					),
					array(
						'label' => 'Добавить шаблон',
						'route' => 'admin/slash/template',
						'resource' => 'admin',
						'action'     => 'addTemplate',
						'params' => array(
							'subdomain' => 'admin',
						),
					),
					array(
						'label' => 'Список разделов',
						'route' => 'admin/slash/template',
						'resource' => 'admin',
						'action'     => 'listSection',
						'params' => array(
							'subdomain' => 'admin',
						),
					),
					array(
						'label' => 'Добавить раздел',
						'route' => 'admin/slash/template',
						'resource' => 'admin',
						'action'     => 'addSection',
						'params' => array(
							'subdomain' => 'admin',
						),
					),
					array(
						'label' => 'Редактировать',
						'route' => 'admin/slash/template',
						'resource' => 'adminnovis',
						'action'     => 'redactorTemplate',
						'params' => array(
							'subdomain' => 'admin',
						),
					),
					array(
						'label' => 'Редактировать',
						'route' => 'admin/slash/template',
						'resource' => 'adminnovis',
						'action'     => 'redactorSection',
						'params' => array(
							'subdomain' => 'admin',
						),
					),
				),
			),
			array(
				'label' => 'Контент',
				'route' => 'admin/slash/content',
				'resource' => 'content',
				'action' => 'index',
				'params' => array(
					'subdomain' => 'admin',
				),
				'pages' => array(
					array(
						'label' => 'Список контента',
						'route' => 'admin/slash/content',
						'resource' => 'content',
						'action'     => 'index',
						'params' => array(
							'subdomain' => 'admin',
						),
					),
					array(
						'label' => 'Список контента сайта',
						'route' => 'admin/slash/content',
						'resource' => 'admin',
						'action'     => 'index-sites',
						'params' => array(
							'subdomain' => 'admin',
						),
					),
					array(
						'label' => 'Добавить контент',
						'route' => 'admin/slash/content',
						'resource' => 'admin',
						'action'     => 'addContent',
						'params' => array(
							'subdomain' => 'admin',
						),
					),
                    array(
                        'label' => 'Список брэндов',
                        'route' => 'admin/slash/content',
                        'resource' => 'admin',
                        'action'     => 'listBrand',
                        'params' => array(
                            'subdomain' => 'admin',
                        ),
                    ),
                    array(
                        'label' => 'Добавить брэнд',
                        'route' => 'admin/slash/content',
                        'resource' => 'admin',
                        'action'     => 'addBrand',
                        'params' => array(
                            'subdomain' => 'admin',
                        ),
                    ),
                    array(
                        'label' => 'Список моделей',
                        'route' => 'admin/slash/content',
                        'resource' => 'admin',
                        'action'     => 'listModel',
                        'params' => array(
                            'subdomain' => 'admin',
                        ),
                    ),
                    array(
                        'label' => 'Добавить модель',
                        'route' => 'admin/slash/content',
                        'resource' => 'admin',
                        'action'     => 'addModel',
                        'params' => array(
                            'subdomain' => 'admin',
                        ),
                    ),

					array(
						'label' => 'Редактировать',
						'route' => 'admin/slash/content',
						'resource' => 'adminnovis',
						'action'     => 'redactorContent',
						'params' => array(
							'subdomain' => 'admin',
						),
					),

				),
			),


	        array(
		        'label' => 'Заказы',
		        'route' => 'admin/slash/pay',
		        'resource' => 'admin',
		        'action' => 'index',
		        'params' => array(
			        'subdomain' => 'admin',
		        ),
		        'pages' => array(
			        array(
				        'label' => 'Список покупателей',
				        'route' => 'admin/slash/pay',
				        'resource' => 'admin',
				        'action'     => 'index',
				        'params' => array(
					        'subdomain' => 'admin',
				        ),
			        ),
			        array(
				        'label' => 'Список товара',
				        'route' => 'admin/slash/pay',
				        'resource' => 'admin',
				        'action'     => 'listPayProduct',
				        'params' => array(
					        'subdomain' => 'admin',
				        ),
			        ),
			        array(
				        'label' => 'Редактировать',
				        'route' => 'admin/slash/pay',
				        'resource' => 'adminnovis',
				        'action'     => 'redactorPay',
				        'params' => array(
					        'subdomain' => 'admin',
				        ),
			        ),
		        ),
	        ),

	        array(
		        'label' => 'Парсеры',
		        'route' => 'admin/slash/parser',
		        'resource' => 'admin',
		        'action' => 'index',
		        'params' => array(
			        'subdomain' => 'admin',
		        ),
		        'pages' => array(
			        array(
				        'label' => 'Список парсеров',
				        'route' => 'admin/slash/parser',
				        'resource' => 'admin',
				        'action'     => 'index',
				        'params' => array(
					        'subdomain' => 'admin',
				        ),
			        ),


		        ),
	        ),

			array(
				'label' => 'Выход',
				'route' => 'admin/slash/logout',
				'resource' => 'logout',
				'params' => array(
					'subdomain' => 'admin',
				),
        ),
		),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'admin' => __DIR__ . '/../view',

        ),
    ),
);
