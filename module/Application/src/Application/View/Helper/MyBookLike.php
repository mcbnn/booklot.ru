<?php

namespace Application\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Application\Entity\MyBookStatus as MyBookStatusEntity;
use Application\Entity\MyBookStatusName as MyBookStatusName;
use Application\Entity\MyBookLike as MyBookLikeEntity;

class MyBookLike extends AbstractHelper
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

        $findOneBy = $em->getRepository(MyBookLikeEntity::class)->findOneBy(
                [
                    'book' => $book_id,
                    'user' => $user->id
                ]
            );
        $findAll = $em->getRepository(MyBookLikeEntity::class)->findOneBy(
            [
                'book' => $book_id
            ]
        );

        return $this->getView()->render('application/button/my-book-like',
            [
                'id'         => $book_id,
                'like'   => $findOneBy,
                'count_like' => count($findAll)
            ]
        );

    }


}