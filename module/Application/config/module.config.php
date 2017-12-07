<?php
/**
 * Zend Framework (http://framework.zend.com/]
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c] 2005-2013 Zend Technologies USA Inc. (http://www.zend.com]
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return [
    'controllers' => [
        'invokables' => [
            'Application\Controller\Index'     => 'Application\Controller\IndexController',
            'Application\Controller\Technical' => 'Application\Controller\TechnicalController',
            'Application\Controller\Auth'      => 'Application\Controller\AuthController',
            'Application\Controller\Cabinet'   => 'Application\Controller\CabinetController',
            'Application\Controller\Events'    => 'Application\Controller\EventsController',
            'Application\Controller\MyBook'    => 'Application\Controller\MyBookController',
            'Application\Controller\MyLike'    => 'Application\Controller\MyLikeController',
            'Application\Controller\Comments'  => 'Application\Controller\CommentsController',
            'Application\Controller\MyBookStatus'  => 'Application\Controller\MyBookStatusController',
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
        'aliases'            => [
            'translator' => 'MvcTranslator',
        ],
        'invokables'         => [
            'Main' => 'Application\Controller\MainController',
        ],
        'factories'          => [
            'Application\Cache\Redis' => 'Application\Service\Factory\RedisFactory',
            'User' => 'Application\Factory\UserFactory',
        ],
    ],
    'view_helpers'    => [
        'factories'  => [
            'button_sort' => 'Application\Factory\ButtonSortFactory',
        ],
        'invokables' => [
            'my_book' => 'Application\View\Helper\MyBook',
            'my_book_status' => 'Application\View\Helper\MyBookStatus',
            'my_book_like' => 'Application\View\Helper\MyBookLike',
            'comments' => 'Application\View\Helper\Comments',
        ],
    ],
    'doctrine'        => [
        'driver' => [
            'Application_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__.'/../src/Application/Entity'],
            ],
            'orm_default'        => [
                'drivers' => [
                    'Application\Entity' => 'Application_driver',
                ],
            ],
        ],
    ],
    'console'         => [
        'router' => [
            'routes' => [
                'parser'     => [
                    'options' => [
                        'route'    => 'parser',
                        'defaults' => [
                            'controller' => 'Application\Controller\Technical',
                            'action'     => 'parser',
                        ],
                    ],
                ],
                'count-book' => [
                    'options' => [
                        'route'    => 'count-book',
                        'defaults' => [
                            'controller' => 'Application\Controller\Technical',
                            'action'     => 'count-book',
                        ],
                    ],
                ],
                'sitemap'    => [
                    'options' => [
                        'route'    => 'sitemap',
                        'defaults' => [
                            'controller' => 'Application\Controller\Technical',
                            'action'     => 'sitemap',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'router'          => [
        'routes' => [
            'home' => [
                'type'          => 'Segment',
                'options'       => [
                    'route'       => '/[[:paged]/]',
                    'constraints' => [
                        'paged' => '[0-9]*',
                    ],
                    'defaults'    => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                        'paged'      => "",
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'series'        => [
                        'type'          => 'Segment',
                        'options'       => [
                            'route'       => 'series[/[:page]]/',
                            'constraints' => [
                                'page' => '[0-9]{1,}',
                            ],
                            'defaults'    => [
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'series',
                                'page'       => "",
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'one' => [
                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'       => '[:alias_menu][/[:page_series]]/',
                                    'constraints' => [
                                        'alias_menu'  => '[a-zA-Z0-9-]{1,}',
                                        'page_series' => '[0-9]{1,}',
                                    ],
                                    'defaults'    => [
                                        'controller'  => 'Application\Controller\Index',
                                        'action'      => 'seriesone',
                                        'page_series' => "",
                                    ],
                                ],

                                'may_terminate' => true,
                                'child_routes'  => [
                                    'book' => [
                                        'type'          => 'Zend\Mvc\Router\Http\Segment',
                                        'options'       => [
                                            'route'       => 'book/[:book/]',
                                            'constraints' => [
                                                'book' => '[a-zA-Z0-9-]{1,}',
                                            ],
                                            'defaults'    => [
                                                'controller' => 'Application\Controller\Index',
                                                'action'     => 'sbook',
                                            ],
                                        ],
                                        'may_terminate' => true,
                                        'child_routes'  => [
                                            'read'    => [
                                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                                'options' => [
                                                    'route'       => 'read/[:page_str/]',
                                                    'constraints' => [
                                                        'page_str' => '[0-9]*',
                                                    ],
                                                    'defaults'    => [
                                                        'controller' => 'Application\Controller\Index',
                                                        'action'     => 'sread',
                                                        'page_str'   => "",
                                                    ],
                                                ],
                                            ],
                                            'content' => [
                                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                                'options' => [
                                                    'route'       => 'content/[:content/]',
                                                    'constraints' => [
                                                        'content' => '[a-zA-Z0-9-]{1,}',
                                                    ],
                                                    'defaults'    => [
                                                        'controller' => 'Application\Controller\Index',
                                                        'action'     => 'scontent',
                                                        'content'    => "",
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'translit'      => [
                        'type'          => 'Segment',
                        'options'       => [
                            'route'       => 'translit[/[:page]]/',
                            'constraints' => [
                                'page' => '[0-9]{1,}',
                            ],
                            'defaults'    => [
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'translit',
                                'page'       => "",
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'one' => [
                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'       => '[:alias_menu][/[:page_translit]]/',
                                    'constraints' => [
                                        'alias_menu'    => '[a-zA-Z0-9-]{2,}',
                                        'page_translit' => '[0-9]{1,}',
                                    ],
                                    'defaults'    => [
                                        'controller'    => 'Application\Controller\Index',
                                        'action'        => 'translitone',
                                        'page_translit' => "",
                                    ],
                                ],

                                'may_terminate' => true,
                                'child_routes'  => [
                                    'book' => [
                                        'type'          => 'Zend\Mvc\Router\Http\Segment',
                                        'options'       => [
                                            'route'       => 'book/[:book/]',
                                            'constraints' => [
                                                'book' => '[a-zA-Z0-9-]{1,}',
                                            ],
                                            'defaults'    => [
                                                'controller' => 'Application\Controller\Index',
                                                'action'     => 'tbook',
                                            ],
                                        ],
                                        'may_terminate' => true,
                                        'child_routes'  => [
                                            'read'    => [
                                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                                'options' => [
                                                    'route'       => 'read/[:page_str/]',
                                                    'constraints' => [
                                                        'page_str' => '[0-9]*',
                                                    ],
                                                    'defaults'    => [
                                                        'controller' => 'Application\Controller\Index',
                                                        'action'     => 'tread',
                                                        'page_str'   => "",
                                                    ],
                                                ],
                                            ],
                                            'content' => [
                                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                                'options' => [
                                                    'route'       => 'content/[:content/]',
                                                    'constraints' => [
                                                        'content' => '[0-9][a-zA-Z0-9-]{1,}',
                                                    ],
                                                    'defaults'    => [
                                                        'controller' => 'Application\Controller\Index',
                                                        'action'     => 'tcontent',
                                                        'content'    => "",
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'authors'       => [
                        'type'          => 'Segment',
                        'options'       => [
                            'route'       => 'authors[/[:page]]/',
                            'constraints' => [
                                'page' => '[0-9]{1,}',
                            ],
                            'defaults'    => [
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'authors',
                                'page'       => "",
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'one' => [
                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'       => '[:alias_menu][/[:page_author]]/',
                                    'constraints' => [
                                        'alias_menu'  => '[a-zA-Z0-9-]{2,}',
                                        'page_author' => '[0-9]{1,}',
                                    ],
                                    'defaults'    => [
                                        'controller'  => 'Application\Controller\Index',
                                        'action'      => 'author',
                                        'page_author' => "",
                                    ],
                                ],

                                'may_terminate' => true,
                                'child_routes'  => [
                                    'book' => [
                                        'type'          => 'Zend\Mvc\Router\Http\Segment',
                                        'options'       => [
                                            'route'       => 'book/[:book/]',
                                            'constraints' => [
                                                'book' => '[a-zA-Z0-9-]{1,}',
                                            ],
                                            'defaults'    => [
                                                'controller' => 'Application\Controller\Index',
                                                'action'     => 'abook',
                                            ],
                                        ],
                                        'may_terminate' => true,
                                        'child_routes'  => [
                                            'read'    => [
                                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                                'options' => [
                                                    'route'       => 'read/[:page_str/]',
                                                    'constraints' => [
                                                        'page_str' => '[0-9]*',
                                                    ],
                                                    'defaults'    => [
                                                        'controller' => 'Application\Controller\Index',
                                                        'action'     => 'aread',
                                                        'page_str'   => "",
                                                    ],
                                                ],
                                            ],
                                            'content' => [
                                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                                'options' => [
                                                    'route'       => 'content/[:content/]',
                                                    'constraints' => [
                                                        'content' => '[a-zA-Z0-9-]{1,}',
                                                    ],
                                                    'defaults'    => [
                                                        'controller' => 'Application\Controller\Index',
                                                        'action'     => 'acontent',
                                                        'content'    => "",
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'problem-avtor' => [
                        'type'    => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'       => 'blocked-book/[:alias_menu]/',
                            'constraints' => [
                                'alias_menu' => '[a-zA-Z0-9-]{4,}',
                            ],
                            'defaults'    => [
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'problem-avtor',
                            ],
                        ],
                    ],
                    'genre'         => [
                        'type'          => 'Literal',
                        'options'       => [
                            'route'    => 'genre/',
                            'defaults' => [
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'genre',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'one' => [
                                'type'          => 'Zend\Mvc\Router\Http\Segment',
                                'options'       => [
                                    'route'       => '[[:s]/][:alias_menu][/[:page]]/',
                                    'constraints' => [
                                        's'          => '[a-zA-Z0-9-]*',
                                        'alias_menu' => '[a-zA-Z0-9-]{4,}',
                                        'page'       => '[0-9]*',
                                    ],
                                    'defaults'    => [
                                        'controller' => 'Application\Controller\Index',
                                        'action'     => 'one_genre',
                                        'page'       => "",
                                        's'          => "",
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes'  => [
                                    'book' => [
                                        'type'          => 'Zend\Mvc\Router\Http\Segment',
                                        'options'       => [
                                            'route'       => 'book/[:book/]',
                                            'constraints' => [
                                                'book' => '[a-zA-Z0-9-]{1,}',
                                            ],
                                            'defaults'    => [
                                                'controller' => 'Application\Controller\Index',
                                                'action'     => 'book',
                                            ],
                                        ],
                                        'may_terminate' => true,
                                        'child_routes'  => [
                                            'read'    => [
                                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                                'options' => [
                                                    'route'       => 'read/[:page_str/]',
                                                    'constraints' => [
                                                        'page_str' => '[0-9]*',
                                                    ],
                                                    'defaults'    => [
                                                        'controller' => 'Application\Controller\Index',
                                                        'action'     => 'read',
                                                        'page_str'   => "",
                                                    ],
                                                ],
                                            ],
                                            'content' => [
                                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                                'options' => [
                                                    'route'       => 'content/[:content/]',
                                                    'constraints' => [
                                                        'content' => '[a-zA-Z0-9-]{1,}',
                                                    ],
                                                    'defaults'    => [
                                                        'controller' => 'Application\Controller\Index',
                                                        'action'     => 'content',
                                                        'content'    => "",
                                                    ],
                                                ],
                                            ],

                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'login'         => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => 'login/',
                            'defaults' => [
                                'controller' => 'Application\Controller\Auth',
                                'action'     => 'login',
                            ],
                        ],
                    ],
                    'test'          => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => 'test/',
                            'defaults' => [
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'test',
                            ],
                        ],
                    ],
                    'log'           => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => 'log/',
                            'defaults' => [
                                'controller' => 'Application\Controller\Auth',
                                'action'     => 'log',
                            ],
                        ],
                    ],
                    'ajaxsearch'    => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => 'ajaxsearch/',
                            'defaults' => [
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'ajaxsearch',
                            ],
                        ],
                    ],
                    'search'        => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'       => 'search/[[:page]/]',
                            'constraints' => [
                                'paged' => '[0-9]*',
                            ],
                            'defaults'    => [
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'search',
                                'paged'      => "",
                            ],
                        ],
                    ],
                    'cabinet'       => [
                        'type'          => 'Literal',
                        'options'       => [
                            'route'    => 'cabinet/',
                            'defaults' => [
                                'controller' => 'Application\Controller\Cabinet',
                                'action'     => 'edit',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'comments' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'       => 'comments/[:page/]',
                                    'constraints' => [
                                        'page' => '[0-9]*',
                                    ],
                                    'defaults'    => [
                                        'controller' => 'Application\Controller\Cabinet',
                                        'action'     => 'comments',
                                    ],
                                ],
                            ],
                            'my-book-status'  => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'       => 'my-book-status/[:page/]',
                                    'constraints' => [
                                        'page' => '[0-9]*',
                                    ],
                                    'defaults'    => [
                                        'controller' => 'Application\Controller\MyBookStatus',
                                        'action'     => 'list',
                                    ],
                                ],
                            ],
                            'my-book'  => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'       => 'my-book/[:page/]',
                                    'constraints' => [
                                        'page' => '[0-9]*',
                                    ],
                                    'defaults'    => [
                                        'controller' => 'Application\Controller\MyBook',
                                        'action'     => 'list',
                                    ],
                                ],
                            ],
                            'my-like'  => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'       => 'my-like/[:page/]',
                                    'constraints' => [
                                        'page' => '[0-9]*',
                                    ],
                                    'defaults'    => [
                                        'controller' => 'Application\Controller\MyLike',
                                        'action'     => 'list',
                                    ],
                                ],
                            ],

                        ],


                    ],
                    'stars'         => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => 'stars/',
                            'defaults' => [
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'stars',
                            ],
                        ],
                    ],
                    'buttons'       => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'       => '[:action/]',
                            'constraints' => [
                                'action' => 'add-my-book|add-status-book|add-book-like',
                            ],
                            'defaults'    => [
                                'controller' => 'Application\Controller\Events',
                            ],
                        ],
                    ],
                    'comments'       => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'       => 'comments/[:action/]',
                            'constraints' => [
                                'action' => 'add|delete',
                            ],
                            'defaults'    => [
                                'controller' => 'Application\Controller\Comments',
                            ],
                        ],
                    ],
                    'logout'        => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => 'logout/',
                            'defaults' => [
                                'controller' => 'Application\Controller\Auth',
                                'action'     => 'logout',
                            ],
                        ],
                    ],
                    'auth'          => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => 'authenticate/',
                            'defaults' => [
                                'controller' => 'Application\Controller\Auth',
                                'action'     => 'authenticate',
                            ],
                        ],
                    ],
                    'reg'           => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => 'reg/',
                            'defaults' => [
                                'controller' => 'Application\Controller\Auth',
                                'action'     => 'reg',
                            ],
                        ],
                    ],
                    'confirm'       => [
                        'type'    => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'       => 'confirm/[:confirm]/',
                            'constraints' => [
                                'action' => '[a-zA-Z0-9-]*',
                            ],
                            'defaults'    => [
                                'controller' => 'Application\Controller\Auth',
                                'action'     => 'confirm',
                            ],
                        ],
                    ],
                    'rightholder'   => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => 'rightholder/',
                            'defaults' => [
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'rightholder',
                            ],
                        ],
                    ],
                    'old'           => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'       => 'oldsite/',
                            'constraints' => [
                                'action' => '[a-zA-Z0-9-]{1,}',
                            ],
                            'defaults'    => [
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'old',
                            ],
                        ],
                    ],
                    'sitemaps'      => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => 'sitemaps/',
                            'defaults' => [
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'sitemaps',
                            ],
                        ],
                    ],
                    'teh'           => [
                        'type'    => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'       => 'tehnical/[:action]/',
                            'constraints' => [
                                'action' => '[a-zA-Z0-9-]*',
                            ],
                            'defaults'    => [
                                'controller' => 'Application\Controller\Technical',
                                'action'     => 'teh',
                            ],
                        ],
                    ],
                    'parser'        => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => 'parser/',
                            'defaults' => [
                                'controller' => 'Application\Controller\Parser',
                                'action'     => 'go',
                            ],
                        ],
                    ],
                    'rider'         => [
                        'type'    => 'Zend\Mvc\Router\Http\Segment',
                        'options' => [
                            'route'       => '[/[:rider]/]',
                            'constraints' => [
                                'rider' => 'zhanr|listbookread|readbook',
                            ],
                            'defaults'    => [
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'rider',
                            ],
                        ],
                    ],
                    'book'          => [
                        'type'          => 'Zend\Mvc\Router\Http\Segment',
                        'options'       => [
                            'route'       => 'book/[:book/]',
                            'constraints' => [
                                'book' => '[a-zA-Z0-9-]{1,}',
                            ],
                            'defaults'    => [
                                'controller' => 'Application\Controller\Index',
                                'action'     => 'sbook',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'read'    => [
                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'       => 'read/[:page_str/]',
                                    'constraints' => [
                                        'page_str' => '[0-9]*',
                                    ],
                                    'defaults'    => [
                                        'controller' => 'Application\Controller\Index',
                                        'action'     => 'sread',
                                        'page_str'   => "",
                                    ],
                                ],
                            ],
                            'content' => [
                                'type'    => 'Zend\Mvc\Router\Http\Segment',
                                'options' => [
                                    'route'       => 'content/[:content/]',
                                    'constraints' => [
                                        'content' => '[a-zA-Z0-9-]{1,}',
                                    ],
                                    'defaults'    => [
                                        'controller' => 'Application\Controller\Index',
                                        'action'     => 'scontent',
                                        'content'    => "",
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'translator'      => [
        'locale'                    => 'en_US',
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__.'/../language',
                'pattern'  => '%s.mo',
            ],
        ],
    ],
    'view_manager'    => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map'             => [
            'layout/layout'           => __DIR__.'/../view/layout/layout.phtml',
            'application/index/index' => __DIR__
                .'/../view/application/index/index.phtml',
            'error/404'               => __DIR__.'/../view/error/404.phtml',
            'error/index'             => __DIR__.'/../view/error/index.phtml',
        ],
        'template_path_stack'      => [
            __DIR__.'/../view',
        ],
        'strategies'               => [
            'ViewJsonStrategy',
        ],
    ],

    'module_layouts' => [
        'default' => [
            'default' => 'layout/layout',
        ],
        'Admin'   => [
            'Admin' => 'layout/admin_layout',
        ],
    ],


];
