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
use Application\Model\History;
use Application\Model\HistoryTable;
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
        $e->getApplication()->getEventManager()->attach('route', array($this, 'checkAuth'));

    }

    public function checkAuth(MvcEvent $e)
    {
        $route = $e->getRouteMatch()->getParams();
        $routeMatch = $e->getRouteMatch();
        $accessArrayController = [
            'Application\Controller\Cabinet',
            'Application\Controller\MyBook',
            'Application\Controller\MyLike',
            'Application\Controller\MyBookStatus'
        ];
        if (
            !$e->getApplication()->getServiceManager()->get('AuthService')->hasIdentity()
            and
            in_array( $e->getRouteMatch()->getParam('controller'), $accessArrayController)
            ){
	        $url = $e->getRouter()->assemble(

                $routeMatch->getParams(),
                [
                    'name' => 'home/login',
                ]

            );
            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);
            $response->sendHeaders();
            return $response;
        }

        $arrUser = $e->getApplication()->getServiceManager()->get('AuthService')->getIdentity();
        $e->getViewModel()->setVariable('arrUser', $arrUser);

        if (isset($arrUser) and !empty($arrUser)) {
            $sm = $e->getApplication()->getServiceManager();
            $arr = array();
            $arr['id_user'] = $arrUser->id;
            $arr['post'] = $e->getRequest()->getPost()->toString();
            $arr['request'] = $e->getRequest()->toString();
            $arr['datetime_created'] = date('Y-m-d H:i:s');
            $arr['url'] = json_encode($route);
            $sm -> get('Application\Model\HistoryTable')->save($arr);

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
                'arraySort' => function(){

                    $arraySort = [
                        'default' => [
                            'sort'      => 'date_add',
                            'direction' => 'desc',
                        ],
                        'params'  => [
                            'date_add' => [
                                'name'   => 'Дата',
                                'column' => 'date_add',
                            ],
                            'visit'    => [
                                'name'    => 'Просмотры',
                                'columnn' => 'visit',
                            ],
                            'name'     => [
                                'name'    => 'Название',
                                'columnn' => 'name',
                            ],
                            'stars'    => [
                                'name'   => 'Рейтинг',
                                'column' => 'stars',
                            ],
                            'kol_str'  => [
                                'name'   => 'Кол. страниц',
                                'column' => 'kol_str',
                            ],
                            'count_stars'  => [
                                'name'   => 'Кол. голосов',
                                'column' => 'count_stars',
                            ],
                        ],
                        'filters' => [
                            'date_add',
                            'visit',
                            'name',
                            'stars',
                            'kol_str',
                            'count_stars'
                        ]
                    ];

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
                    $table = new MZhanrTable($tableGateway);
                    return $table;
                },
                'MZhanrTable\Gateway' => function ($sm) {
                    $dbAdapter = $sm->get('Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new MZhanr());
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
				//history
				'Application\Model\HistoryTable' => function ($sm) {
					$tableGateway = $sm->get('HistoryTable\Gateway');
					$table = new HistoryTable($tableGateway);
					return $table;
				},
				'HistoryTable\Gateway' => function ($sm) {
					$dbAdapter = $sm->get('AdapterFirst');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new History());
					return new TableGateway('history', $dbAdapter, null, $resultSetPrototype);
				},
                //history
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
