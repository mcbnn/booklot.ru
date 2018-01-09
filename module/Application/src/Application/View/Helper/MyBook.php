<?php

namespace Application\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Application\Entity\MyBook as MyBookEntity;

class MyBook extends AbstractHelper
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
        $repository = $em->getRepository(MyBookEntity::class);
        $my_book = false;
        if($user) {
            $findOneBy = $repository->findOneBy(
                [
                    'book' => $book_id,
                    'user' => $user->id
                ]
            );
        if($findOneBy){
            $my_book = true;
        }
        }
        return $this->getView()->render('application/button/my-book',
            [
                'id' => $book_id,
                'my_book' => $my_book
            ]
        );

    }


}