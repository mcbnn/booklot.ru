<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array(
    'view_helpers' => array(
        'factories'=> array(
            'button_sort' => 'Application\Factory\ButtonSortFactory',
        ),
        'invokables' => [
            'my_book' => 'Application\View\Helper\MyBook'
        ]
    ),
    'doctrine' => array(
        'driver' => array(
            'Application_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/Application/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Application\Entity' =>  'Application_driver'
                ),
            ),
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'parser' => array(
                    'options' => array(
                        'route'    => 'parser',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Technical',
                            'action'     => 'parser'
                        )
                    )
                ),
                'count-book' => array(
                    'options' => array(
                        'route'    => 'count-book',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Technical',
                            'action'     => 'count-book'
                        )
                    )
                ),
                'sitemap' => array(
                    'options' => array(
                        'route'    => 'sitemap',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Technical',
                            'action'     => 'sitemap'
                        )
                    )
                ),
            )
        )
    ),
    'router'          => array(
        'routes' => array(
            'home'   => array(
                'type'          => 'Segment',
                'options'       => array(
                    'route'       => '/[[:paged]/]',
                    'constraints' => array(
                        'paged' => '[0-9]*',
                    ),
                    'defaults'    => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                        'paged'      => "",
                    ),
                ),
                'may_terminate' => true,
                'child_routes'  => array(
                    'series' => array(
                        'type'          => 'Segment',
                        'options'       => array(
                            'route'       => 'series[/[:page]]/',
                            'constraints' => array(
                                'page' => '[0-9]{1,}',
                            ),
                            'defaults'    => array(
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'series',
                                'page'       => "",
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes'  => array(
                            'one' => array(
                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                'options' => array(
                                    'route'       => '[:alias_menu][/[:page_series]]/',
                                    'constraints' => array(
                                        'alias_menu'  => '[a-zA-Z0-9-]{1,}',
                                        'page_series' => '[0-9]{1,}',
                                    ),
                                    'defaults'    => array(
                                        'controller'  => 'Application\Controller\Index',
                                        'action'      => 'seriesone',
                                        'page_series' => "",
                                    ),
                                ),

                                'may_terminate' => true,
                                'child_routes'  => array(
                                    'book' => array(
                                        'type'          => 'Zend\Mvc\Router\Http\Segment',
                                        'options'       => array(
                                            'route'       => 'book/[:book/]',
                                            'constraints' => array(
                                                'book' => '[a-zA-Z0-9-]{1,}',
                                            ),
                                            'defaults'    => array(
                                                'controller' => 'Application\Controller\Index',
                                                'action'     => 'sbook',
                                            ),
                                        ),
                                        'may_terminate' => true,
                                        'child_routes'  => array(
                                            'read'    => array(
                                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                                'options' => array(
                                                    'route'       => 'read/[:page_str/]',
                                                    'constraints' => array(
                                                        'page_str' => '[0-9]*',
                                                    ),
                                                    'defaults'    => array(
                                                        'controller' => 'Application\Controller\Index',
                                                        'action'     => 'sread',
                                                        'page_str'   => "",
                                                    ),
                                                ),
                                            ),
                                            'content' => array(
                                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                                'options' => array(
                                                    'route'       => 'content/[:content/]',
                                                    'constraints' => array(
                                                        'content' => '[a-zA-Z0-9-]{1,}',
                                                    ),
                                                    'defaults'    => array(
                                                        'controller' => 'Application\Controller\Index',
                                                        'action'     => 'scontent',
                                                        'content'    => "",
                                                    ),
                                                ),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'translit' => array(
                        'type'          => 'Segment',
                        'options'       => array(
                            'route'       => 'translit[/[:page]]/',
                            'constraints' => array(
                                'page' => '[0-9]{1,}',
                            ),
                            'defaults'    => array(
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'translit',
                                'page'       => "",
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes'  => array(
                            'one' => array(
                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                'options' => array(
                                    'route'       => '[:alias_menu][/[:page_translit]]/',
                                    'constraints' => array(
                                        'alias_menu'    => '[a-zA-Z0-9-]{2,}',
                                        'page_translit' => '[0-9]{1,}',
                                    ),
                                    'defaults'    => array(
                                        'controller'    => 'Application\Controller\Index',
                                        'action'        => 'translitone',
                                        'page_translit' => "",
                                    ),
                                ),

                                'may_terminate' => true,
                                'child_routes'  => array(
                                    'book' => array(
                                        'type'          => 'Zend\Mvc\Router\Http\Segment',
                                        'options'       => array(
                                            'route'       => 'book/[:book/]',
                                            'constraints' => array(
                                                'book' => '[a-zA-Z0-9-]{1,}',
                                            ),
                                            'defaults'    => array(
                                                'controller' => 'Application\Controller\Index',
                                                'action'     => 'tbook',
                                            ),
                                        ),
                                        'may_terminate' => true,
                                        'child_routes'  => array(
                                            'read'    => array(
                                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                                'options' => array(
                                                    'route'       => 'read/[:page_str/]',
                                                    'constraints' => array(
                                                        'page_str' => '[0-9]*',
                                                    ),
                                                    'defaults'    => array(
                                                        'controller' => 'Application\Controller\Index',
                                                        'action'     => 'tread',
                                                        'page_str'   => "",
                                                    ),
                                                ),
                                            ),
                                            'content' => array(
                                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                                'options' => array(
                                                    'route'       => 'content/[:content/]',
                                                    'constraints' => array(
                                                        'content' => '[0-9][a-zA-Z0-9-]{1,}',
                                                    ),
                                                    'defaults'    => array(
                                                        'controller' => 'Application\Controller\Index',
                                                        'action'     => 'tcontent',
                                                        'content'    => "",
                                                    ),
                                                ),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'authors'  => array(
                        'type'          => 'Segment',
                        'options'       => array(
                            'route'       => 'authors[/[:page]]/',
                            'constraints' => array(
                                'page' => '[0-9]{1,}',
                            ),
                            'defaults'    => array(
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'authors',
                                'page'       => "",
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes'  => array(
                            'one' => array(
                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                'options' => array(
                                    'route'       => '[:alias_menu][/[:page_author]]/',
                                    'constraints' => array(
                                        'alias_menu'  => '[a-zA-Z0-9-]{2,}',
                                        'page_author' => '[0-9]{1,}',
                                    ),
                                    'defaults'    => array(
                                        'controller'  => 'Application\Controller\Index',
                                        'action'      => 'author',
                                        'page_author' => "",
                                    ),
                                ),

                                'may_terminate' => true,
                                'child_routes'  => array(
                                    'book' => array(
                                        'type'          => 'Zend\Mvc\Router\Http\Segment',
                                        'options'       => array(
                                            'route'       => 'book/[:book/]',
                                            'constraints' => array(
                                                'book' => '[a-zA-Z0-9-]{1,}',
                                            ),
                                            'defaults'    => array(
                                                'controller' => 'Application\Controller\Index',
                                                'action'     => 'abook',
                                            ),
                                        ),
                                        'may_terminate' => true,
                                        'child_routes'  => array(
                                            'read'    => array(
                                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                                'options' => array(
                                                    'route'       => 'read/[:page_str/]',
                                                    'constraints' => array(
                                                        'page_str' => '[0-9]*',
                                                    ),
                                                    'defaults'    => array(
                                                        'controller' => 'Application\Controller\Index',
                                                        'action'     => 'aread',
                                                        'page_str'   => "",
                                                    ),
                                                ),
                                            ),
                                            'content' => array(
                                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                                'options' => array(
                                                    'route'       => 'content/[:content/]',
                                                    'constraints' => array(
                                                        'content' => '[a-zA-Z0-9-]{1,}',
                                                    ),
                                                    'defaults'    => array(
                                                        'controller' => 'Application\Controller\Index',
                                                        'action'     => 'acontent',
                                                        'content'    => "",
                                                    ),
                                                ),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'problem-avtor' => array(
                        'type'    => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route'       => 'blocked-book/[:alias_menu]/',
                            'constraints' => array(
                                'alias_menu' => '[a-zA-Z0-9-]{4,}',
                            ),
                            'defaults'    => array(
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'problem-avtor',
                            ),
                        ),
                    ),
                    'genre' => array(
                        'type'          => 'Literal',
                        'options'       => array(
                            'route'    => 'genre/',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'genre',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes'  => array(
                            'one' => array(
                                'type'          => 'Zend\Mvc\Router\Http\Segment',
                                'options'       => array(
                                    'route'       => '[[:s]/][:alias_menu][/[:page]]/',
                                    'constraints' => array(
                                        's'          => '[a-zA-Z0-9-]*',
                                        'alias_menu' => '[a-zA-Z0-9-]{4,}',
                                        'page'       => '[0-9]*',
                                    ),
                                    'defaults'    => array(
                                        'controller' => 'Application\Controller\Index',
                                        'action'     => 'one_genre',
                                        'page'       => "",
                                        's'          => "",
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes'  => array(
                                    'book' => array(
                                        'type'          => 'Zend\Mvc\Router\Http\Segment',
                                        'options'       => array(
                                            'route'       => 'book/[:book/]',
                                            'constraints' => array(
                                                'book' => '[a-zA-Z0-9-]{1,}',
                                            ),
                                            'defaults'    => array(
                                                'controller' => 'Application\Controller\Index',
                                                'action'     => 'book',
                                            ),
                                        ),
                                        'may_terminate' => true,
                                        'child_routes'  => array(
                                            'read'    => array(
                                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                                'options' => array(
                                                    'route'       => 'read/[:page_str/]',
                                                    'constraints' => array(
                                                        'page_str' => '[0-9]*',
                                                    ),
                                                    'defaults'    => array(
                                                        'controller' => 'Application\Controller\Index',
                                                        'action'     => 'read',
                                                        'page_str'   => "",
                                                    ),
                                                ),
                                            ),
                                            'content' => array(
                                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                                'options' => array(
                                                    'route'       => 'content/[:content/]',
                                                    'constraints' => array(
                                                        'content' => '[a-zA-Z0-9-]{1,}',
                                                    ),
                                                    'defaults'    => array(
                                                        'controller' => 'Application\Controller\Index',
                                                        'action'     => 'content',
                                                        'content'    => "",
                                                    ),
                                                ),
                                            ),

                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'login'      => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => 'login/',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Auth',
                                'action'     => 'login',
                            ),
                        ),
                    ),
                    'test'        => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => 'test/',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'test',
                            ),
                        ),
                    ),
                    'log'        => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => 'log/',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Auth',
                                'action'     => 'log',
                            ),
                        ),
                    ),
                    'ajaxsearch' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => 'ajaxsearch/',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'ajaxsearch',
                            ),
                        ),
                    ),
                    'search'     => array(
                        'type'          => 'Segment',
                        'options'       => array(
                            'route'       => 'search/[[:page]/]',
                            'constraints' => array(
                                'paged' => '[0-9]*',
                            ),
                            'defaults'    => array(
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'search',
                                'paged'      => "",
                            ),
                        ),
                    ),
                    'cabinet'       => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => 'cabinet/',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Cabinet',
                                'action'     => 'edit',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes'  => [
                            'comments' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => 'comments/[:page/]',
                                    'constraints' => array(
                                        'page' => '[0-9]*',
                                    ),
                                    'defaults' => [
                                        'controller' => 'Application\Controller\Cabinet',
                                        'action'     => 'comments',
                                    ],
                                ]
                            ],
                            'my-book' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => 'my-book/[:page/]',
                                    'constraints' => array(
                                        'page' => '[0-9]*',
                                    ),
                                    'defaults' => [
                                        'controller' => 'Application\Controller\MyBook',
                                        'action'     => 'list',
                                    ],
                                ]
                            ]

                        ]


                    ),
                    'stars'      => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => 'stars/',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'stars',
                            ),
                        ),
                    ),
                    'buttons'    => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'       => '[:action/]',
                            'constraints' => array(
                                'action' => 'add-my-book',
                            ),
                            'defaults'    => array(
                                'controller' => 'Application\Controller\Events',
                            ),
                        ),
                    ),
                    'comment'    => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'       => 'comment/[:action/]',
                            'constraints' => array(
                                'action' => 'add|online|del|red-comm|cit-comm',
                            ),
                            'defaults'    => array(
                                'controller' => 'Application\Controller\Index',
                            ),
                        ),
                    ),
                    'logout'     => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => 'logout/',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Auth',
                                'action'     => 'logout',
                            ),
                        ),
                    ),
                    'auth'       => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => 'authenticate/',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Auth',
                                'action'     => 'authenticate',
                            ),
                        ),
                    ),
                    'reg'        => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => 'reg/',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Auth',
                                'action'     => 'reg',
                            ),
                        ),
                    ),
                    'confirm'    => array(
                        'type'    => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route'       => 'confirm/[:confirm]/',
                            'constraints' => array(
                                'action' => '[a-zA-Z0-9-]*',
                            ),
                            'defaults'    => array(
                                'controller' => 'Application\Controller\Auth',
                                'action'     => 'confirm',
                            ),
                        ),
                    ),
                    'rightholder' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => 'rightholder/',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'rightholder',
                            ),
                        ),
                    ),
                    'old'         => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'       => 'oldsite/',
                            'constraints' => array(
                                'action' => '[a-zA-Z0-9-]{1,}',
                            ),
                            'defaults'    => array(
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'old',
                            ),
                        ),
                    ),
                    'sitemaps'    => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => 'sitemaps/',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'sitemaps',
                            ),
                        ),
                    ),
                    'teh'         => array(
                        'type'    => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route'       => 'tehnical/[:action]/',
                            'constraints' => array(
                                'action' => '[a-zA-Z0-9-]*',
                            ),
                            'defaults'    => array(
                                'controller' => 'Application\Controller\Technical',
                                'action'     => 'teh',
                            ),
                        ),
                    ),
                    'parser'      => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => 'parser/',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Parser',
                                'action'     => 'go',
                            ),
                        ),
                    ),
                    'rider'       => array(
                        'type'    => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route'       => '[/[:rider]/]',
                            'constraints' => array(
                                'rider' => 'zhanr|listbookread|readbook',
                            ),
                            'defaults'    => array(
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'rider',
                            ),
                        ),
                    ),
                    'book'        => array(
                        'type'          => 'Zend\Mvc\Router\Http\Segment',
                        'options'       => array(
                            'route'       => 'book/[:book/]',
                            'constraints' => array(
                                'book' => '[a-zA-Z0-9-]{1,}',
                            ),
                            'defaults'    => array(
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'sbook',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes'  => array(
                            'read'    => array(
                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                'options' => array(
                                    'route'       => 'read/[:page_str/]',
                                    'constraints' => array(
                                        'page_str' => '[0-9]*',
                                    ),
                                    'defaults'    => array(
                                        'controller' => 'Application\Controller\Index',
                                        'action'     => 'sread',
                                        'page_str'   => "",
                                    ),
                                ),
                            ),
                            'content' => array(
                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                'options' => array(
                                    'route'       => 'content/[:content/]',
                                    'constraints' => array(
                                        'content' => '[a-zA-Z0-9-]{1,}',
                                    ),
                                    'defaults'    => array(
                                        'controller' => 'Application\Controller\Index',
                                        'action'     => 'scontent',
                                        'content'    => "",
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases'            => array(
            'translator' => 'MvcTranslator',
        ),
        'invokables'         => array(
            'Main' => 'Application\Controller\MainController',
        ),
        'factories' => array(
            'Application\Cache\Redis' => 'Application\Service\Factory\RedisFactory',
        )
    ),
    'translator'      => array(
        'locale'                    => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__.'/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index'     => 'Application\Controller\IndexController',
            'Application\Controller\Technical' => 'Application\Controller\TechnicalController',
            'Application\Controller\Auth'      => 'Application\Controller\AuthController',
            'Application\Controller\Cabinet'   => 'Application\Controller\CabinetController',
            'Application\Controller\Events'    => 'Application\Controller\EventsController',
            'Application\Controller\MyBook'    => 'Application\Controller\MyBookController',
        ),
    ),
    'view_manager'    => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map'             => array(
            'layout/layout'           => __DIR__.'/../view/layout/layout.phtml',
            'application/index/index' => __DIR__
                .'/../view/application/index/index.phtml',
            'error/404'               => __DIR__.'/../view/error/404.phtml',
            'error/index'             => __DIR__.'/../view/error/index.phtml',
        ),
        'template_path_stack'      => array(
            __DIR__.'/../view',
        ),
        'strategies'               => array(
            'ViewJsonStrategy',
        ),
    ),

    'module_layouts' => array(
        'default' => array(
            'default' => 'layout/layout',
        ),
        'Admin'   => array(
            'Admin' => 'layout/admin_layout',
        ),
    ),


);
