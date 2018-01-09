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
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Entity\Bogi;

class UserFactory implements FactoryInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em = null;

    /**
     * @param ServiceLocatorInterface $sm
     *
     * @return \Doctrine\ORM\EntityManager|mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function getEntityManager(ServiceLocatorInterface $sm)
    {
        if ($this->em == null) {
            $this->em = $sm->get(
                'doctrine.entitymanager.orm_default'
            );
        }
        return $this->em;
    }

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return Bogi|null|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $sm = $container->get('ServiceManager');
        $user = $sm->get('AuthService')->getIdentity();
        $em = $this->getEntityManager($sm);
        $repository = $em->getRepository(Bogi::class);

        if(!$user){
            $user = new Bogi();
        }
        else{
            $user = $repository->find($user->id);
        }
        return $user;
    }

}