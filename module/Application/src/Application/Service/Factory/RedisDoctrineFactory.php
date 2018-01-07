<?php
namespace Application\Service\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Cache\ZendStorageCache;

class RedisDoctrineFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $serviceLocator) {
        $redis_cache = $serviceLocator->get('Application\Cache\Redis');
        $doctrine_cache = new ZendStorageCache($redis_cache);
        return $doctrine_cache;
    }
}