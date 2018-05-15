<?php
/**
 * Zend Framework (http://framework.zend.com/]
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c] 2005-2013 Zend Technologies USA Inc. (http://www.zend.com]
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\Controller\LazyControllerAbstractFactory;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'controllers'     => [
        'factories' => [
            Controller\IndexController::class         => LazyControllerAbstractFactory::class,
            Controller\AuthController::class          => LazyControllerAbstractFactory::class,
            Controller\ArticlesController::class      => LazyControllerAbstractFactory::class,
            Controller\TopController::class           => LazyControllerAbstractFactory::class,
            Controller\EventsController::class        => LazyControllerAbstractFactory::class,
            Controller\CommentsController::class      => LazyControllerAbstractFactory::class,
            Controller\CabinetController::class       => LazyControllerAbstractFactory::class,
            Controller\MyBookController::class        => LazyControllerAbstractFactory::class,
            Controller\MyLikeController::class        => LazyControllerAbstractFactory::class,
            Controller\MyBookStatusController::class  => LazyControllerAbstractFactory::class,
            Controller\AdminArticlesController::class => LazyControllerAbstractFactory::class,
            Controller\AdminBookController::class     => LazyControllerAbstractFactory::class,
            Controller\AdminAvtorController::class    => LazyControllerAbstractFactory::class,
            Controller\AdminSeriiController::class    => LazyControllerAbstractFactory::class,
            Controller\AdminTranslitController::class => LazyControllerAbstractFactory::class,
            Controller\AdminSoderController::class    => LazyControllerAbstractFactory::class,
            Controller\AdminTextController::class     => LazyControllerAbstractFactory::class,
            Controller\AdminFilesController::class    => LazyControllerAbstractFactory::class,
            Controller\AdminFbController::class       => LazyControllerAbstractFactory::class,
            Controller\RssController::class           => LazyControllerAbstractFactory::class,
            Controller\TechnicalController::class     => LazyControllerAbstractFactory::class,
            Controller\AdminAdController::class => LazyControllerAbstractFactory::class,
            Controller\MailController::class => LazyControllerAbstractFactory::class,
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
            'book' => 'Application\Factory\BookFactory',
            'NavigationDynamic' =>   'Application\Service\NavigationDynamicFactory',
            'Application\Cache\Redis' => 'Application\Service\Factory\RedisFactory',
            'User'                    => 'Application\Factory\UserFactory',
            'AjaxSearch'                    => 'Application\Factory\AjaxSearchFactory',
            'doctrine.cache.my_redis' => 'Application\Service\Factory\RedisDoctrineFactory',
        ],
    ],
    'view_helpers'    => [
        'factories'  => [
            'button_sort' => 'Application\Factory\ButtonSortFactory',
            'button_search' => 'Application\Factory\ButtonSearchFactory',
            'User'                    => 'Application\Factory\UserFactory',
            'button' => Factory\ButtonFactory::class,
            'permission' => Factory\PermissionFactory::class,
            'ad' => Factory\AdFactory::class
        ],
        'invokables' => [
            'my_book'        => 'Application\View\Helper\MyBook',
            'my_book_status' => 'Application\View\Helper\MyBookStatus',
            'my_book_like'   => 'Application\View\Helper\MyBookLike',
            'comments'       => 'Application\View\Helper\Comments',
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
                'dubleavtor' => [
                    'options' => [
                        'route'    => 'dubleavtor',
                        'defaults' => [
                            'controller' => Controller\TechnicalController::class,
                            'action'     => 'dubleavtor',
                        ],
                    ],
                ],
                'dubletranslit' => [
                    'options' => [
                        'route'    => 'dubletranslit',
                        'defaults' => [
                            'controller' => Controller\TechnicalController::class,
                            'action'     => 'dubletranslit',
                        ],
                    ],
                ],


                'dublealias' => [
                    'options' => [
                        'route'    => 'dublealias',
                        'defaults' => [
                            'controller' => Controller\TechnicalController::class,
                            'action'     => 'dublealias',
                        ],
                    ],
                ],
                'series' => [
                    'options' => [
                        'route'    => 'series',
                        'defaults' => [
                            'controller' => Controller\TechnicalController::class,
                            'action'     => 'series',
                        ],
                    ],
                ],
                'checkfoto'     => [
                    'options' => [
                        'route'    => 'checkfoto',
                        'defaults' => [
                            'controller' => Controller\TechnicalController::class,
                            'action'     => 'checkfoto',
                        ],
                    ],
                ],
                'parser'     => [
                    'options' => [
                        'route'    => 'parser',
                        'defaults' => [
                            'controller' => Controller\TechnicalController::class,
                            'action'     => 'parser',
                        ],
                    ],
                ],
                'bookalias'     => [
                    'options' => [
                        'route'    => 'bookalias',
                        'defaults' => [
                            'controller' => Controller\TechnicalController::class,
                            'action'     => 'check-alias-book',
                        ],
                    ],
                ],
                'count-book' => [
                    'options' => [
                        'route'    => 'count-book',
                        'defaults' => [
                            'controller' => Controller\TechnicalController::class,
                            'action'     => 'count-book',
                        ],
                    ],
                ],
                'type-files' => [
                    'options' => [
                        'route'    => 'type-files',
                        'defaults' => [
                            'controller' => Controller\TechnicalController::class,
                            'action'     => 'type-files',
                        ],
                    ],
                ],
                'sitemap'    => [
                    'options' => [
                        'route'    => 'sitemap',
                        'defaults' => [
                            'controller' => Controller\TechnicalController::class,
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
                'type'          => Literal::class,
                'options'       => [
                    'route'       => '/',
                    'defaults'    => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'page'     => [
                        'type'          => Segment::class,
                        'options'       => [
                            'route'       => '[:page]/',
                            'constraints' => [
                                'page' => '[0-9]*',
                            ],
                            'defaults'    => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'index',
                            ]
                        ],
                    ],
                    'mail'           => [
                        'type'    =>  Segment::class,
                        'options'       => [
                            'route'       => 'mail/[:type/]',
                            'constraints' => [
                                'type' => '[0-9]*',
                            ],
                            'defaults'    => [
                                'controller' => Controller\MailController::class,
                                'action'     => 'index',
                                'type'      => 1
                            ]
                        ],
                    ],
                    'ad-iframe'           => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '[:alias]',
                            'constraints' => [
                                'alias'          => 'ad-iframe/undefined/|ad-iframe/',
                            ],
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'ad-iframe',
                            ],
                        ],
                    ],
                    'ad-iframe2'           => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '[:alias]',
                            'constraints' => [
                                'alias'          => 'ad-iframe2/undefined/|ad-iframe2/',
                            ],
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'ad-iframe2',
                            ],
                        ],
                    ],
                    'ad-iframe3'           => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '[:alias]',
                            'constraints' => [
                                'alias'          => 'ad-iframe3/undefined/|ad-iframe3/',
                            ],
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'ad-iframe3',
                            ],
                        ],
                    ],
                    'ad-iframe4'           => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '[:alias]',
                            'constraints' => [
                                'alias'          => 'ad-iframe4/undefined/|ad-iframe4/',
                            ],
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'ad-iframe4',
                            ],
                        ],
                    ],
                    'series'         => [
                        'type'          => Segment::class,
                        'options'       => [
                            'route'       => 'series[/[:page]]/',
                            'constraints' => [
                                'page' => '[0-9]{1,}',
                            ],
                            'defaults'    => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'series',
                                'page'       => "",
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'one' => [
                                'type'    => Segment::class,
                                'options' => [
                                    'route'       => '[:alias_menu][/[:page_series]]/',
                                    'constraints' => [
                                        'alias_menu'  => '[a-zA-Z0-9-]{1,}',
                                        'page_series' => '[0-9]{1,}',
                                    ],
                                    'defaults'    => [
                                        'controller'  => Controller\IndexController::class,
                                        'action'      => 'seriesone',
                                        'page_series' => "",
                                    ],
                                ],

                                'may_terminate' => true,
                                'child_routes'  => [
                                    'book' => [
                                        'type'          => Segment::class,
                                        'options'       => [
                                            'route'       => 'book/[:book/]',
                                            'constraints' => [
                                                'book' => '[a-zA-Z0-9-]{1,}',
                                            ],
                                            'defaults'    => [
                                                'controller' => Controller\IndexController::class,
                                                'action'     => 'sbook',
                                            ],
                                        ],
                                        'may_terminate' => true,
                                        'child_routes'  => [
                                            'read'    => [
                                                'type'    => Segment::class,
                                                'options' => [
                                                    'route'       => 'read/[:page_str/]',
                                                    'constraints' => [
                                                        'page_str' => '[0-9]*',
                                                    ],
                                                    'defaults'    => [
                                                        'controller' => Controller\IndexController::class,
                                                        'action'     => 'sread',
                                                        'page_str'   => "",
                                                    ],
                                                ],
                                            ],
                                            'content' => [
                                                'type'    => Segment::class,
                                                'options' => [
                                                    'route'       => 'content/[:content/]',
                                                    'constraints' => [
                                                        'content' => '[a-zA-Z0-9-]{1,}',
                                                    ],
                                                    'defaults'    => [
                                                        'controller' => Controller\IndexController::class,
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
                    'translit'       => [
                        'type'          => Segment::class,
                        'options'       => [
                            'route'       => 'translit[/[:page]]/',
                            'constraints' => [
                                'page' => '[0-9]{1,}',
                            ],
                            'defaults'    => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'translit',
                                'page'       => "",
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'one' => [
                                'type'    => Segment::class,
                                'options' => [
                                    'route'       => '[:alias_menu][/[:page_translit]]/',
                                    'constraints' => [
                                        'alias_menu'    => '[a-zA-Z0-9-]{2,}',
                                        'page_translit' => '[0-9]{1,}',
                                    ],
                                    'defaults'    => [
                                        'controller'    => Controller\IndexController::class,
                                        'action'        => 'translitone',
                                        'page_translit' => "",
                                    ],
                                ],

                                'may_terminate' => true,
                                'child_routes'  => [
                                    'book' => [
                                        'type'          => Segment::class,
                                        'options'       => [
                                            'route'       => 'book/[:book/]',
                                            'constraints' => [
                                                'book' => '[a-zA-Z0-9-]{1,}',
                                            ],
                                            'defaults'    => [
                                                'controller' => Controller\IndexController::class,
                                                'action'     => 'tbook',
                                            ],
                                        ],
                                        'may_terminate' => true,
                                        'child_routes'  => [
                                            'read'    => [
                                                'type'    => Segment::class,
                                                'options' => [
                                                    'route'       => 'read/[:page_str/]',
                                                    'constraints' => [
                                                        'page_str' => '[0-9]*',
                                                    ],
                                                    'defaults'    => [
                                                        'controller' => Controller\IndexController::class,
                                                        'action'     => 'tread',
                                                        'page_str'   => "",
                                                    ],
                                                ],
                                            ],
                                            'content' => [
                                                'type'    => Segment::class,
                                                'options' => [
                                                    'route'       => 'content/[:content/]',
                                                    'constraints' => [
                                                        'content' => '[a-zA-Z0-9-]{1,}',
                                                    ],
                                                    'defaults'    => [
                                                        'controller' => Controller\IndexController::class,
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
                    'authors'        => [
                        'type'          => Segment::class,
                        'options'       => [
                            'route'       => 'authors[/[:page]]/',
                            'constraints' => [
                                'page' => '[0-9]{1,}',
                            ],
                            'defaults'    => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'authors',
                                'page'       => "",
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'one' => [
                                'type'    => Segment::class,
                                'options' => [
                                    'route'       => '[:alias_menu][/[:page_author]]/',
                                    'constraints' => [
                                        'alias_menu'  => '[a-zA-Z0-9-]{2,}',
                                        'page_author' => '[0-9]{1,}',
                                    ],
                                    'defaults'    => [
                                        'controller'  => Controller\IndexController::class,
                                        'action'      => 'author',
                                        'page_author' => "",
                                    ],
                                ],

                                'may_terminate' => true,
                                'child_routes'  => [
                                    'book' => [
                                        'type'          => Segment::class,
                                        'options'       => [
                                            'route'       => 'book/[:book/]',
                                            'constraints' => [
                                                'book' => '[a-zA-Z0-9-]{1,}',
                                            ],
                                            'defaults'    => [
                                                'controller' => Controller\IndexController::class,
                                                'action'     => 'abook',
                                            ],
                                        ],
                                        'may_terminate' => true,
                                        'child_routes'  => [
                                            'read'    => [
                                                'type'    => Segment::class,
                                                'options' => [
                                                    'route'       => 'read/[:page_str/]',
                                                    'constraints' => [
                                                        'page_str' => '[0-9]*',
                                                    ],
                                                    'defaults'    => [
                                                        'controller' => Controller\IndexController::class,
                                                        'action'     => 'aread',
                                                        'page_str'   => "",
                                                    ],
                                                ],
                                            ],
                                            'content' => [
                                                'type'    => Segment::class,
                                                'options' => [
                                                    'route'       => 'content/[:content/]',
                                                    'constraints' => [
                                                        'content' => '[a-zA-Z0-9-]{1,}',
                                                    ],
                                                    'defaults'    => [
                                                        'controller' => Controller\IndexController::class,
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
                    'problem-avtor'  => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'       => 'blocked-book/[:book]/',
                            'constraints' => [
                                'book' => '[a-zA-Z0-9-]{4,}',
                            ],
                            'defaults'    => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'problem-avtor',
                            ],
                        ],
                    ],
                    'genre'          => [
                        'type'          => Literal::class,
                        'options'       => [
                            'route'    => 'genre/',
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'genre',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'one' => [
                                'type'          => Segment::class,
                                'options'       => [
                                    'route'       => '[[:s]/][:alias_menu][/[:page]]/',
                                    'constraints' => [
                                        's'          => '[a-zA-Z0-9-]*',
                                        'alias_menu' => '[a-z][a-zA-Z0-9-]*',
                                        'page'       => '[0-9]*',
                                    ],
                                    'defaults'    => [
                                        'controller' => Controller\IndexController::class,
                                        'action'     => 'one_genre',
                                        'page'       => "",
                                        's'          => "",
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes'  => [
                                    'book' => [
                                        'type'          => Segment::class,
                                        'options'       => [
                                            'route'       => 'book/[:book/]',
                                            'constraints' => [
                                                'book' => '[a-zA-Z0-9-]{1,}',
                                            ],
                                            'defaults'    => [
                                                'controller' => Controller\IndexController::class,
                                                'action'     => 'book',
                                            ],
                                        ],
                                        'may_terminate' => true,
                                        'child_routes'  => [
                                            'read'    => [
                                                'type'    => Segment::class,
                                                'options' => [
                                                    'route'       => 'read/[:page_str/]',
                                                    'constraints' => [
                                                        'page_str' => '[0-9]*',
                                                    ],
                                                    'defaults'    => [
                                                        'controller' => Controller\IndexController::class,
                                                        'action'     => 'read',
                                                        'page_str'   => "",
                                                    ],
                                                ],
                                            ],
                                            'content' => [
                                                'type'    => Segment::class,
                                                'options' => [
                                                    'route'       => 'content/[:content/]',
                                                    'constraints' => [
                                                        'content' => '[a-zA-Z0-9-]{1,}',
                                                    ],
                                                    'defaults'    => [
                                                        'controller' => Controller\IndexController::class,
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
                    'login'          => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => 'login/',
                            'defaults' => [
                                'controller' => Controller\AuthController::class,
                                'action'     => 'login',
                            ],
                        ],
                    ],
                    'test'           => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => 'test/',
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'test',
                            ],
                        ],
                    ],
                    'log'            => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => 'log/',
                            'defaults' => [
                                'controller' => Controller\AuthController::class,
                                'action'     => 'log',
                            ],
                        ],
                    ],
                    'ajaxsearch'     => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => 'ajaxsearch/',
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'ajaxsearch',
                            ],
                        ],
                    ],
                    'search'         => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'       => 'search/[[:page]/]',
                            'constraints' => [
                                'paged' => '[0-9]*',
                            ],
                            'defaults'    => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'search',
                                'paged'      => "",
                            ],
                        ],
                    ],
                    'cabinet'        => [
                        'type'          => Literal::class,
                        'options'       => [
                            'route'    => 'cabinet/',
                            'defaults' => [
                                'controller' => Controller\CabinetController::class,
                                'action'     => 'edit',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'comments'       => [
                                'type'    => Segment::class,
                                'options' => [
                                    'route'       => 'comments/[:page/]',
                                    'constraints' => [
                                        'page' => '[0-9]*',
                                    ],
                                    'defaults'    => [
                                        'controller' => Controller\CabinetController::class,
                                        'action'     => 'comments',
                                    ],
                                ],
                            ],
                            'my-book-status' => [
                                'type'    => Segment::class,
                                'options' => [
                                    'route'       => 'my-book-status/[:page/]',
                                    'constraints' => [
                                        'page' => '[0-9]*',
                                    ],
                                    'defaults'    => [
                                        'controller' => Controller\MyBookStatusController::class,
                                        'action'     => 'list',
                                    ],
                                ],
                            ],
                            'my-book'        => [
                                'type'    => Segment::class,
                                'options' => [
                                    'route'       => 'my-book/[:page/]',
                                    'constraints' => [
                                        'page' => '[0-9]*',
                                    ],
                                    'defaults'    => [
                                        'controller' => Controller\MyBookController::class,
                                        'action'     => 'list',
                                    ],
                                ],
                            ],
                            'my-like'        => [
                                'type'    => Segment::class,
                                'options' => [
                                    'route'       => 'my-like/[:page/]',
                                    'constraints' => [
                                        'page' => '[0-9]*',
                                    ],
                                    'defaults'    => [
                                        'controller' =>  Controller\MyLikeController::class,
                                        'action'     => 'list',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'stars'          => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => 'stars/',
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'stars',
                            ],
                        ],
                    ],
                    'buttons'        => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'       => '[:action/]',
                            'constraints' => [
                                'action' => 'add-my-book|add-status-book|add-book-like',
                            ],
                            'defaults'    => [
                                'controller' => Controller\EventsController::class,
                            ],
                        ],
                    ],
                    'comments'       => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'       => 'comments/[:action/]',
                            'constraints' => [
                                'action' => 'add|delete',
                            ],
                            'defaults'    => [
                                'controller' => Controller\CommentsController::class,
                            ],
                        ],
                    ],
                    'logout'         => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => 'logout/',
                            'defaults' => [
                                'controller' => Controller\AuthController::class,
                                'action'     => 'logout',
                            ],
                        ],
                    ],
                    'auth'           => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => 'authenticate/',
                            'defaults' => [
                                'controller' => Controller\AuthController::class,
                                'action'     => 'authenticate',
                            ],
                        ],
                    ],
                    'reg'            => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => 'reg/',
                            'defaults' => [
                                'controller' => Controller\AuthController::class,
                                'action'     => 'reg',
                            ],
                        ],
                    ],
                    'confirm'        => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'       => 'confirm/[:confirm]/',
                            'constraints' => [
                                'action' => '[a-zA-Z0-9-]*',
                            ],
                            'defaults'    => [
                                'controller' => Controller\AuthController::class,
                                'action'     => 'confirm',
                            ],
                        ],
                    ],
                    'rightholder'    => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => 'rightholder/',
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'rightholder',
                            ],
                        ],
                    ],
                    'old'            => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'       => 'oldsite/',
                            'constraints' => [
                                'action' => '[a-zA-Z0-9-]{1,}',
                            ],
                            'defaults'    => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'old',
                            ],
                        ],
                    ],
                    'sitemaps'       => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => 'sitemaps/',
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'sitemaps',
                            ],
                        ],
                    ],
                    'teh'            => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'       => 't/[:action]/',
                            'constraints' => [
                                'action' => '[a-zA-Z0-9-]*',
                            ],
                            'defaults'    => [
                                'controller' => Controller\TechnicalController::class,
                                'action'     => 'teh',
                            ],
                        ],
                    ],
                    'parser'         => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => 'parser/',
                            'defaults' => [
                                'controller' => 'Application\Controller\Parser',
                                'action'     => 'go',
                            ],
                        ],
                    ],
                    'rider'          => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'       => '[/[:rider]/]',
                            'constraints' => [
                                'rider' => 'zhanr|listbookread|readbook',
                            ],
                            'defaults'    => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'rider',
                            ],
                        ],
                    ],
                    'book'           => [
                        'type'          => Segment::class,
                        'options'       => [
                            'route'       => 'book/[:book/]',
                            'constraints' => [
                                'book' => '[a-zA-Z0-9-]{1,}',
                            ],
                            'defaults'    => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'sbook',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'read'    => [
                                'type'    => Segment::class,
                                'options' => [
                                    'route'       => 'read/[:page_str/]',
                                    'constraints' => [
                                        'page_str' => '[0-9]*',
                                    ],
                                    'defaults'    => [
                                        'controller' => Controller\IndexController::class,
                                        'action'     => 'sread',
                                        'page_str'   => "",
                                    ],
                                ],
                            ],
                            'content' => [
                                'type'    => Segment::class,
                                'options' => [
                                    'route'       => 'content/[:content/]',
                                    'constraints' => [
                                        'content' => '[a-zA-Z0-9-]{1,}',
                                    ],
                                    'defaults'    => [
                                        'controller' => Controller\IndexController::class,
                                        'action'     => 'scontent',
                                        'content'    => "",
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'rss'            => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => 'feed.xml',
                            'defaults' => [
                                'controller' => Controller\RssController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'ad-stat-add'            => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => 'ad-stat-add/',
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'ad-stat-add',
                            ],
                        ],
                    ],
                    'articles'       => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'       => 'articles/[:page/]',
                            'constraints' => [
                                'page' => '[0-9]*',
                            ],
                            'defaults'    => [
                                'controller' => Controller\ArticlesController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'article'        => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'       => 'article/[:alias/]',
                            'constraints' => [
                                'alias' => '[a-zA-Z0-9-]{1,}',
                            ],
                            'defaults'    => [
                                'controller' => Controller\ArticlesController::class,
                                'action'     => 'article',
                            ],
                        ],
                    ],
                    'tops'           => [
                        'type'          => Literal::class,
                        'options'       => [
                            'route'    => 'tops/',
                            'defaults' => [
                                'controller' => Controller\TopController::class,
                                'action'     => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'one' => [
                                'type'    => Segment::class,
                                'options' => [
                                    'route'       => '[:alias/]',
                                    'constraints' => [
                                        'alias' => '[a-zA-Z0-9-]{1,}',
                                    ],
                                    'defaults'    => [
                                        'controller' => Controller\TopController::class,
                                        'action'     => 'top',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'admin-articles' => [
                        'type'          => Segment::class,
                        'options'       => [
                            'route'       => 'admin-articles/[:page]',
                            'constraints' => [
                                'page' => '[0-9]*',
                            ],
                            'defaults'    => [
                                'controller' => Controller\AdminArticlesController::class,
                                'action'     => 'index',
                                'page'       => false,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'event' => [
                                'type'    => Segment::class,
                                'options' => [
                                    'route'       => '[:action/][:id/]',
                                    'constraints' => [
                                        'action' => 'add|edit|delete',
                                        'id'     => '[0-9]*',
                                    ],
                                    'defaults'    => [
                                        'controller' => Controller\AdminArticlesController::class,
                                        'action'     => 'index',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'admin-book'     => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'       => 'admin-book/book[/:action][/:id]/',
                            'constraints' => [
                                'action' => 'add|edit|delete',
                                'id'     => '[0-9]*',
                            ],
                            'defaults'    => [
                                'controller' => Controller\AdminBookController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'admin-avtor'    => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'       => 'admin-book/avtor[/:id]/',
                            'constraints' => [
                                'id' => '[0-9]*',
                            ],
                            'defaults'    => [
                                'controller' => Controller\AdminAvtorController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'admin-serii'    => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'       => 'admin-book/serii[/:id]/',
                            'constraints' => [
                                'id' => '[0-9]*',
                            ],
                            'defaults'    => [
                                'controller' => Controller\AdminSeriiController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'admin-translit' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'       => 'admin-book/translit[/:id]/',
                            'constraints' => [
                                'id' => '[0-9]*',
                            ],
                            'defaults'    => [
                                'controller' => Controller\AdminTranslitController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'admin-soder'    => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'       => 'admin-book/soder[/:id]/',
                            'constraints' => [
                                'id' => '[0-9]*',
                            ],
                            'defaults'    => [
                                'controller' => Controller\AdminSoderController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'admin-text'    => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'       => 'admin-book/text/[:action][/:id]/',
                            'constraints' => [
                                'id' => '[0-9]*',
                                'action' => 'edit|edittext|add|delete'
                            ],
                            'defaults'    => [
                                'controller' => Controller\AdminTextController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'admin-fb'    => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'       => 'admin-book/fb[/:action][/:id]/',
                            'constraints' => [
                                'id' => '[0-9]*',
                                'action' => 'index|edit|add|delete|convert|test'
                            ],
                            'defaults'    => [
                                'controller' => Controller\AdminFbController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'admin-files'    => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'       => 'admin-book/files[/:id]/',
                            'constraints' => [
                                'id' => '[0-9]*',
                            ],
                            'defaults'    => [
                                'controller' => Controller\AdminFilesController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'admin-ad'    => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'       => 'ad[/:action][/:id][/:page]/',
                            'constraints' => [
                                'page' => '[0-9]*',
                                'id' => '[0-9]*',
                                'action' => 'index|edit|add|delete|stat'
                            ],
                            'defaults'    => [
                                'controller' => Controller\AdminAdController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'download'    => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'       => 'download[/:id_book_files]/',
                            'constraints' => [
                                'id_book_files' => '[0-9]*',
                            ],
                            'defaults'    => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'download',
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
    'module_layouts'  => [
        'default' => [
            'default' => 'layout/layout',
        ],
        'Admin'   => [
            'Admin' => 'layout/admin_layout',
        ],
    ],
];
