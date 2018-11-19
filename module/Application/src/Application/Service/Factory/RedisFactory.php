<?php
namespace Application\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Cache\Storage\Adapter\RedisOptions;
use Zend\Cache\Storage\Adapter\Redis;

class RedisFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return object|Redis
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get ( 'Config' );
        $config = $config ['redis'];
        $redisOptions = new RedisOptions ();
//        $redisOptions->setServer ( array (
//            'host' => $config ["host"],
//            'port' => $config ["port"],
//            'timeout' => 30
//        ) );
	    $redisOptions = new RedisOptions ();
        $redisOptions->setServer ( $config ["uri"] );
        $redisOptions->setTtl(1200);

        $redisOptions->setLibOptions ( [
            \Redis::OPT_SERIALIZER => \Redis::SERIALIZER_PHP
        ] );

        $redis = new Redis ( $redisOptions );
        return $redis;
    }
}