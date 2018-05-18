<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\View\Helper\Permission;

class PermissionFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return Permission|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $AuthService = $container
            ->get('ServiceManager')
            ->get('AuthService');
        $EntityManager = $container
            ->get('ServiceManager')
            ->get('doctrine.entitymanager.orm_default');
        $ServiceManager = $container
            ->get('ServiceManager');
        $request = $container->get('Request');
        return new Permission($AuthService, $EntityManager, $ServiceManager, $request);
    }
}