<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Factory;

use Zend\ServiceManager\FactoryInterface;
use Application\View\Helper\ButtonSort;
use Zend\ServiceManager\ServiceLocatorInterface;

class ButtonSortFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm)
    {
        $sm = $sm->getServiceLocator();
        return new ButtonSort($sm);
    }
}