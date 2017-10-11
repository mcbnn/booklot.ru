<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;


class Module implements AutoloaderProviderInterface
{
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

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Admin\Model\MyAuthStorage' => function () {
                    return new \Admin\Model\MyAuthStorage('zf_tutorial');
                },

                'AuthService'                 => function ($sm) {
                    $dbAdapter = $sm->get('Adapter');
                    $dbTableAuthAdapter = new AuthAdapter($dbAdapter, 'bogi', 'email', 'password', 'status != "compromised"');
                    $columnsToReturn = array(
                        'id', 'name', 'password', 'email', 'birth', 'sex', 'foto', 'comments', 'datetime_reg', 'datetime_log'
                    );
					 
					$select = $dbTableAuthAdapter->getDbSelect();
					$select->where('vis = "1"');
                    $dbTableAuthAdapter->getResultRowObject($columnsToReturn);
                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter);
                    $authService->setStorage($sm->get('Admin\Model\MyAuthStorage'));
                    return $authService;
                },
            ),
        );
    }

}
