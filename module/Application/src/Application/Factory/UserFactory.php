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
use Application\Entity\Bogi;

class UserFactory implements FactoryInterface
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em = null;

    protected function getEntityManager(ServiceLocatorInterface $sm)
    {
        if ($this->em == null) {
            $this->em = $sm->get(
                'doctrine.entitymanager.orm_default'
            );
        }
        return $this->em;
    }

    public function createService(ServiceLocatorInterface $sm)
    {
        $user = $sm->get('AuthService')->getIdentity();
        if($user == null) return [];
        $em = $this->getEntityManager($sm);
        $repository = $em->getRepository(Bogi::class);

        return $repository->find($user->id);
    }
}