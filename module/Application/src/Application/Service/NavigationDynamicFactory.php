<?php

namespace Application\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class NavigationDynamicFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return object|\Zend\Navigation\Navigation
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $navigation = new NavigationDynamic();
        return $navigation->createService($container);
    }
}

?>