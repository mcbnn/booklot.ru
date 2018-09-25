<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;

use Application\Model\MZhanr;
use Application\Model\MZhanrTable;
use Application\Model\Book;
use Application\Model\BookTable;
use Application\Model\MAvtor;
use Application\Model\MAvtorTable;
use Application\Model\MSerii;
use Application\Model\MSeriiTable;
use Application\Model\MTranslit;
use Application\Model\MTranslitTable;
use Application\Model\Soder;
use Application\Model\SoderTable;
use Application\Model\Serii;
use Application\Model\SeriiTable;
use Application\Model\Text;
use Application\Model\TextTable;
use Application\Model\TextDop;
use Application\Model\TextDopTable;
use Application\Model\Translit;
use Application\Model\TranslitTable;
use Application\Model\Zhanr;
use Application\Model\ZhanrTable;
use Application\Model\CommentsFaik;
use Application\Model\CommentsFaikTable;
use Application\Model\CommentsBan;
use Application\Model\CommentsBanTable;
use Application\Model\Comments;
use Application\Model\CommentsTable;
use Application\Model\BookFiles;
use Application\Model\BookFilesTable;
use Application\Model\Bogi;
use Application\Model\BogiTable;
use Application\Model\Avtor;
use Application\Model\AvtorTable;
use Application\Model\BogiVisit;
use Application\Model\BogiVisitTable;
use Application\Model\Stars;
use Application\Model\StarsTable;

class Module
{
	public $admin = false;

    public function onBootstrap(MvcEvent $e)
    {
        {
            $e->getApplication()->getEventManager()->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($e) {
                $controller      = $e->getTarget();
                $controllerClass = get_class($controller);
                $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
                $config          = $e->getApplication()->getServiceManager()->get('config');

                if ($controllerClass == 'Application\Controller\AuthController') {
                        $controller->layout($config['module_layouts']['Admin']['Admin']);
                }
                else{
                    $controller->layout($config['module_layouts']['default']['default']);
                }
            }, 100);
        }

        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        //$e->getApplication()->getEventManager()->attach('route', array($this, 'checkAuth'));

    }

    public function checkAuth(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        $arrUser = $e->getApplication()->getServiceManager()->get('AuthService')->getIdentity();
        $hasIdentity = $e->getApplication()->getServiceManager()->get('AuthService')->hasIdentity();
        $controller = $e->getRouteMatch()->getParam('controller');

        $accessArrayController = [
            'Application\Controller\CabinetController',
            'Application\Controller\MyBookController',
            'Application\Controller\MyLikeController',
            'Application\Controller\MyBookStatusController'
        ];
        $accessAdminArrayController = [
            'Application\Controller\AdminArticlesController',
            'Application\Controller\AdminBookController',
            'Application\Controller\AdminFilesController',
            'Application\Controller\AdminTranslitController',
            'Application\Controller\AdminSeriiController',
            'Application\Controller\AdminAvtorController',
            'Application\Controller\AdminSoderController',
            'Application\Controller\AdminTextController',
            'Application\Controller\AdminFbController',
            'Application\Controller\AdminAdController',
        ];
        if (
            in_array($controller, $accessAdminArrayController)
            or
            in_array($controller, $accessArrayController)
        ) {
            if (!$hasIdentity) {
                $url = $e->getRouter()->assemble(
                    $routeMatch->getParams(),
                    [
                        'name' => 'home',
                    ]
                );
                $response = $e->getResponse();
                $response->getHeaders()->addHeaderLine('Location', $url);
                $response->setStatusCode(302);
                $response->sendHeaders();

                return $response;
            } elseif (
                $arrUser->role != 'admin'
                and
                in_array($controller, $accessAdminArrayController)
            ) {
                $url = $e->getRouter()->assemble(
                    $routeMatch->getParams(),
                    [
                        'name' => 'home',
                    ]
                );
                $response = $e->getResponse();
                $response->getHeaders()->addHeaderLine('Location', $url);
                $response->setStatusCode(302);
                $response->sendHeaders();

                return $response;
            }

        }
        $e->getViewModel()->setVariable('arrUser', $arrUser);

        if (isset($arrUser) and !empty($arrUser)) {

            $sm = $e->getApplication()->getServiceManager();
            $arr = array();
            $arr['user_id'] = $arrUser->id;
            $arr['url'] = $url = $e->getApplication()->getServiceManager()->get('ViewHelperManager')->get('ServerUrl')->__invoke(true);
            $sm -> get('Application\Model\BogiVisitTable')->save($arr);

        }

    }

    public function getConfig()
    {	
		//return include __DIR__ . '/config/module.config1.php';
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'arrayWhere' => function ($sm) {
                    $query = $sm->get('Request')->getQuery();
                    $arrayWhere = [
                        'collapsed' => '1',
                        'params' => [
                            'b_name'      => [
                                'name' => 'Название книги:',
                                'value' => ''
                            ],
                            'b_nameZhanr' => [
                                'name' => 'Жанр:',
                                'value' => ''
                            ],
                            'ma_name' => [
                                'name' => 'Автор:',
                                'value' => ''
                            ],
                            'ms_name' => [
                                'name' => 'Серия:',
                                'value' => ''
                            ],
                            'mt_name' => [
                                'name' => 'Переводчик:',
                                'value' => ''
                            ],
                            'b_year' => [
                                'name' => 'Год:',
                                'value' => ''
                            ],
                            'b_isbn' => [
                                'name' => 'ISBN:',
                                'value' => ''
                            ],
                            'b_city' => [
                                'name' => 'Город:',
                                'value' => ''
                            ],
                            'b_lang' => [
                                'name' => 'Язык:',
                                'value' => ''
                            ],
                            'b_kolStr' => [
                                'name' => 'Кол-во стр-ц(>):',
                                'value' => ''
                            ],
                        ],
                    ];
                    $where = [
                        'b_vis' => [
                            'column'   => 'b.vis',
                            'type'     => '=',
                            'value'    => 1,
                            'operator' => 'and',
                        ],
                    ];
                    foreach ($query as $k => $v) {
                        if (empty(trim($v))){
                            continue;
                        }
                        switch ($k) {
                            case 'b_name':
                                $where[$k]['type'] = "LIKE";
                                $where[$k]['value'] = mb_strtolower("%$v%", 'utf-8');
                                $where[$k]['column'] = 'LOWER(b.name)';
                                $where[$k]['operator'] = 'and';
                                break;
                            case 'b_nameZhanr':
                                $where[$k]['type'] = "LIKE";
                                $where[$k]['value'] = mb_strtolower("%$v%", 'utf-8');
                                $where[$k]['column'] = 'LOWER(b.nameZhanr)';
                                $where[$k]['operator'] = 'and';
                                break;
                            case 'ma_name':
                                $where[$k]['type'] = "LIKE";
                                $where[$k]['value'] = mb_strtolower("%$v%", 'utf-8');
                                $where[$k]['column'] = 'LOWER(ma.name)';
                                $where[$k]['operator'] = 'and';
                                break;
                            case 'ms_name':
                                $where[$k]['type'] = "LIKE";
                                $where[$k]['value'] = mb_strtolower("%$v%", 'utf-8');
                                $where[$k]['column'] = 'LOWER(ms.name)';
                                $where[$k]['operator'] = 'and';
                                break;
                            case 'mt_name':
                                $where[$k]['type'] = "LIKE";
                                $where[$k]['value'] = mb_strtolower("%$v%", 'utf-8');
                                $where[$k]['column'] = 'LOWER(mt.name)';
                                $where[$k]['operator'] = 'and';
                                break;
                            case 'b_year':
                                $where[$k]['type'] = "=";
                                $where[$k]['value'] = "$v";
                                $where[$k]['column'] = 'b.year';
                                $where[$k]['operator'] = 'and';
                                break;
                            case 'b_isbn':
                                $where[$k]['type'] = "LIKE";
                                $where[$k]['value'] = mb_strtolower("$v%", 'utf-8');
                                $where[$k]['column'] = 'LOWER(b.isbn)';
                                $where[$k]['operator'] = 'and';
                                break;
                            case 'b_city':
                                $where[$k]['type'] = "LIKE";
                                $where[$k]['value'] = mb_strtolower("$v%", 'utf-8');
                                $where[$k]['column'] = 'LOWER(b.city)';
                                $where[$k]['operator'] = 'and';
                                break;
                            case 'b_lang':
                                $where[$k]['type'] = "LIKE";
                                $where[$k]['value'] = mb_strtolower("$v%", 'utf-8');
                                $where[$k]['column'] = 'LOWER(b.lang)';
                                $where[$k]['operator'] = 'and';
                                break;
                            case 'b_langOr':
                                $where[$k]['type'] = "LIKE";
                                $where[$k]['value'] = mb_strtolower("$v%", 'utf-8');
                                $where[$k]['column'] = 'LOWER(b.langOr)';
                                $where[$k]['operator'] = 'and';
                                break;
                            case 'b_kolStr':
                                $where[$k]['type'] = ">";
                                $where[$k]['value'] = "$v";
                                $where[$k]['column'] = 'b.kolStr';
                                $where[$k]['operator'] = 'and';
                                break;
                        }
                    }
                    $arrayWhere['where'] = $where;
                    return $arrayWhere;
                },
                'arraySort' => function($sm){
                    $query = $sm->get('Request')->getQuery();
                    $arraySort = [
                        'default' => [
                            'sort'      => 'b.dateAdd',
                            'direction' => 'desc',
                        ],
                        'params'  => [
                            'b.dateAdd' => [
                                'name'   => 'Дата',
                                'column' => 'b.dateAdd',
                            ],
                            'b.visit'    => [
                                'name'    => 'Просмотры',
                                'columnn' => 'b.visit',
                            ],
                            'b.name'     => [
                                'name'    => 'Название',
                                'columnn' => 'b.name',
                            ],
                            'b.stars'    => [
                                'name'   => 'Рейтинг',
                                'column' => 'b.stars',
                            ],
                            'b.kolStr'  => [
                                'name'   => 'Кол. страниц',
                                'column' => 'b.kolStr',
                            ],
                            'b.countStars'  => [
                                'name'   => 'Кол. голосов',
                                'column' => 'b.countStars',
                            ],
                        ],
                        'filters' => [
                            'b.dateAdd',
                            'b.visit',
                            'b.name',
                            'b.stars',
                            'b.kolStr',
                            'b.countStars'
                        ]
                    ];

                    if($arraySort['default']['sort'] == 'b.stars'){
                        $order['b.stars'] = 'desc';
                        $order['b.countStars'] = 'desc';
                    }
                    else{
                        $order[$arraySort['default']['sort']] = $arraySort['default']['direction'];
                    }
                    $sort = $query->get('sort');
                    $direction = ($query->get('direction') == 'desc')? 'desc' : 'asc';
                    if ($sort and in_array(
                            $sort,
                            $arraySort['filters']
                        )
                    ) {
                        unset($order);
                        $order[$sort] = $direction;
                        if ($sort == 'stars') {
                            unset($order);
                            $order[$sort] = $direction;
                            $order['b.countStars'] = 'desc';
                        }

                    }
                    $arraySort['order'] = $order;
                    return $arraySort;

                },
                'Application\Model\MyAuthStorage' => function () {
                    return new \Application\Model\MyAuthStorage('zf_tutorial');
                },
                'AuthService'                 => function ($sm) {
                    $dbAdapter = $sm->get('Adapter');
                    $dbTableAuthAdapter = new AuthAdapter($dbAdapter, 'bogi', 'email', 'password', 'vis = 1');

                    $select = $dbTableAuthAdapter->getDbSelect();
					$select->where("bogi.vis = '1'");
                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter);
                    $authService->setStorage($sm->get('Application\Model\MyAuthStorage'));
                    return $authService;
                },
                'NavigationDynamic' =>   'Application\Service\NavigationDynamicFactory',
                //бренд
                'Application\Model\MZhanrTable' => function ($sm) {
                    $tableGateway = $sm->get('MZhanrTable\Gateway');
                    $cacheAdapter = $sm->get('Application\Cache\Redis'); ;
                    $table = new MZhanrTable($tableGateway);
                    $table->setCache($cacheAdapter);
                    return $table;
                },
                'MZhanrTable\Gateway' => function ($sm) {
                    $dbAdapter = $sm->get('Adapter');
                    $resultSetPrototype = new HydratingResultSet();
                    $resultSetPrototype->setObjectPrototype(new MZhanr());
                    return new TableGateway('m_zhanr', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\BookTable' => function ($sm) {
	                $tableGateway = $sm->get('BookTable\Gateway');
                    $cacheAdapter = $sm->get('Application\Cache\Redis'); ;
	                $table = new BookTable($tableGateway);
                    $table->setCache($cacheAdapter);
	                return $table;
                },
                'BookTable\Gateway' => function ($sm) {
	                $dbAdapter = $sm->get('Adapter');
	                //$resultSetPrototype = new ResultSet();
                    $resultSetPrototype = new HydratingResultSet();
	                $resultSetPrototype->setObjectPrototype(new Book());
	                return new TableGateway('book', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\MAvtorTable' => function ($sm) {
	                $tableGateway = $sm->get('MAvtorTable\Gateway');
	                $table = new MAvtorTable($tableGateway);
	                return $table;
                },
                'MAvtorTable\Gateway' => function ($sm) {
	                $dbAdapter = $sm->get('Adapter');
	                $resultSetPrototype = new ResultSet();
	                $resultSetPrototype->setArrayObjectPrototype(new MAvtor());
	                return new TableGateway('m_avtor', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\MSeriiTable' => function ($sm) {
	                $tableGateway = $sm->get('MSeriiTable\Gateway');
	                $table = new MSeriiTable($tableGateway);
	                return $table;
                },
                'MSeriiTable\Gateway' => function ($sm) {
	                $dbAdapter = $sm->get('Adapter');
	                $resultSetPrototype = new ResultSet();
	                $resultSetPrototype->setArrayObjectPrototype(new MSerii());
	                return new TableGateway('m_serii', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\MTranslitTable' => function ($sm) {
	                $tableGateway = $sm->get('MTranslitTable\Gateway');
	                $table = new MTranslitTable($tableGateway);
	                return $table;
                },
                'MTranslitTable\Gateway' => function ($sm) {
	                $dbAdapter = $sm->get('Adapter');
	                $resultSetPrototype = new ResultSet();
	                $resultSetPrototype->setArrayObjectPrototype(new MTranslit());
	                return new TableGateway('m_translit', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\SoderTable' => function ($sm) {
	                $tableGateway = $sm->get('SoderTable\Gateway');
	                $table = new SoderTable($tableGateway);
	                return $table;
                },
                'SoderTable\Gateway' => function ($sm) {
	                $dbAdapter = $sm->get('Adapter');
	                $resultSetPrototype = new ResultSet();
	                $resultSetPrototype->setArrayObjectPrototype(new Soder());
	                return new TableGateway('soder', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\SeriiTable' => function ($sm) {
	                $tableGateway = $sm->get('SeriiTable\Gateway');
	                $table = new SeriiTable($tableGateway);
	                return $table;
                },
                'SeriiTable\Gateway' => function ($sm) {
	                $dbAdapter = $sm->get('Adapter');
	                $resultSetPrototype = new ResultSet();
	                $resultSetPrototype->setArrayObjectPrototype(new Serii());
	                return new TableGateway('serii', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\TextTable' => function ($sm) {
	                $tableGateway = $sm->get('TextTable\Gateway');
	                $table = new TextTable($tableGateway);
	                return $table;
                },
                'TextTable\Gateway' => function ($sm) {
	                $dbAdapter = $sm->get('Adapter');
	                $resultSetPrototype = new ResultSet();
	                $resultSetPrototype->setArrayObjectPrototype(new Text());
	                return new TableGateway('text', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\TextDopTable' => function ($sm) {
	                $tableGateway = $sm->get('TextDopTable\Gateway');
	                $table = new TextDopTable($tableGateway);
	                return $table;
                },
                'TextDopTable\Gateway' => function ($sm) {
	                $dbAdapter = $sm->get('Adapter');
	                $resultSetPrototype = new ResultSet();
	                $resultSetPrototype->setArrayObjectPrototype(new TextDop());
	                return new TableGateway('text_dop', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\TranslitTable' => function ($sm) {
	                $tableGateway = $sm->get('TranslitTable\Gateway');
	                $table = new TranslitTable($tableGateway);
	                return $table;
                },
                'TranslitTable\Gateway' => function ($sm) {
	                $dbAdapter = $sm->get('Adapter');
	                $resultSetPrototype = new ResultSet();
	                $resultSetPrototype->setArrayObjectPrototype(new Translit());
	                return new TableGateway('translit', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\ZhanrTable' => function ($sm) {
	                $tableGateway = $sm->get('ZhanrTable\Gateway');
	                $table = new ZhanrTable($tableGateway);
	                return $table;
                },
                'ZhanrTable\Gateway' => function ($sm) {
	                $dbAdapter = $sm->get('Adapter');
	                $resultSetPrototype = new ResultSet();
	                $resultSetPrototype->setArrayObjectPrototype(new Zhanr());
	                return new TableGateway('zhanr', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\CommentsFaikTable' => function ($sm) {
		            $tableGateway = $sm->get('CommentsFaikTable\Gateway');
		            $table = new CommentsFaikTable($tableGateway);
		            return $table;
	            },
                'CommentsFaikTable\Gateway' => function ($sm) {
	                $dbAdapter = $sm->get('Adapter');
	                $resultSetPrototype = new ResultSet();
	                $resultSetPrototype->setArrayObjectPrototype(new CommentsFaik());
	                return new TableGateway('comments_faik', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\CommentsBanTable' => function ($sm) {
	                $tableGateway = $sm->get('CommentsBanTable\Gateway');
	                $table = new CommentsBanTable($tableGateway);
	                return $table;
                },
                'CommentsBanTable\Gateway' => function ($sm) {
	                $dbAdapter = $sm->get('Adapter');
	                $resultSetPrototype = new ResultSet();
	                $resultSetPrototype->setArrayObjectPrototype(new CommentsBan());
	                return new TableGateway('comments_ban', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\CommentsTable' => function ($sm) {
	                $tableGateway = $sm->get('CommentsTable\Gateway');
	                $table = new CommentsTable($tableGateway);
	                return $table;
                },
                'CommentsTable\Gateway' => function ($sm) {
	                $dbAdapter = $sm->get('Adapter');
	                $resultSetPrototype = new ResultSet();
	                $resultSetPrototype->setArrayObjectPrototype(new Comments());
	                return new TableGateway('comments', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\BookFilesTable' => function ($sm) {
	                $tableGateway = $sm->get('BookFilesTable\Gateway');
	                $table = new BookFilesTable($tableGateway);
	                return $table;
                },
                'BookFilesTable\Gateway' => function ($sm) {
	                $dbAdapter = $sm->get('Adapter');
	                $resultSetPrototype = new ResultSet();
	                $resultSetPrototype->setArrayObjectPrototype(new BookFiles());
	                return new TableGateway('book_files', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\BogiTable' => function ($sm) {
	                $tableGateway = $sm->get('BogiTable\Gateway');
	                $table = new BogiTable($tableGateway);
	                return $table;
                },
                'BogiTable\Gateway' => function ($sm) {
	                $dbAdapter = $sm->get('Adapter');
	                $resultSetPrototype = new ResultSet();
	                $resultSetPrototype->setArrayObjectPrototype(new Bogi());
	                return new TableGateway('bogi', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\AvtorTable' => function ($sm) {
	                $tableGateway = $sm->get('AvtorTable\Gateway');
	                $table = new AvtorTable($tableGateway);
	                return $table;
                },
                'AvtorTable\Gateway' => function ($sm) {
	                $dbAdapter = $sm->get('Adapter');
	                $resultSetPrototype = new ResultSet();
	                $resultSetPrototype->setArrayObjectPrototype(new Avtor());
	                return new TableGateway('avtor', $dbAdapter, null, $resultSetPrototype);
                },
				//BogiVisit
				'Application\Model\BogiVisitTable' => function ($sm) {
					$tableGateway = $sm->get('BogiVisitTable\Gateway');
					$table = new BogiVisitTable($tableGateway);
					return $table;
				},
				'BogiVisitTable\Gateway' => function ($sm) {
					$dbAdapter = $sm->get('AdapterFirst');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new BogiVisit());
					return new TableGateway('bogi_visit', $dbAdapter, null, $resultSetPrototype);
				},

                'Application\Model\StarsTable' => function ($sm) {
                    $tableGateway = $sm->get('StarsTable\Gateway');
                    $table = new StarsTable($tableGateway);
                    return $table;
                },
                'StarsTable\Gateway' => function ($sm) {
                    $dbAdapter = $sm->get('AdapterFirst');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Stars());
                    return new TableGateway('stars', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }


}
