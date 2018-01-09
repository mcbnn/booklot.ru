<?php
namespace Application\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use DoctrineModule\Cache\ZendStorageCache;

class RedisDoctrineFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $serviceLocator = $container->get('ServiceManager');
        $redis_cache = $serviceLocator->get('Application\Cache\Redis');
        $doctrine_cache = new ZendStorageCache($redis_cache);
        return $doctrine_cache;
    }
}