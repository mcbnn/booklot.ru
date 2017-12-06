<?php

namespace Application\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Application\Entity\MyBookStatus as MyBookStatusEntity;
use Application\Entity\MyBookStatusName as MyBookStatusName;

class MyBookStatus extends AbstractHelper
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em = null;

    protected function getEntityManager()
    {
        if ($this->em == null) {
            $this->em = $this->getView()->getHelperPluginManager()->getServiceLocator()->get(
                'doctrine.entitymanager.orm_default'
            );
        }
        return $this->em;
    }


    public function __invoke($book_id = null)
    {
        if($book_id == null)return [];

        $em = $this->getEntityManager();
        $user = $this->getView()->getHelperPluginManager()->getServiceLocator()->get('AuthService')->getIdentity();
        $repository = $em->getRepository(MyBookStatusName::class);
        $status_all = $repository->findAll();

        $findOneBy = $em->getRepository(MyBookStatusEntity::class)->findOneBy(
                [
                    'book' => $book_id,
                    'user' => $user->id
                ]
            );


        return $this->getView()->render('application/button/my-book-status',
            [
                'id'         => $book_id,
                'status_all' => $status_all,
                'selected_status'   => $findOneBy,
            ]
        );

    }


}