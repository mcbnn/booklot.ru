<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Application\Entity\Comments as CommentsEntity;

class Comments extends AbstractHelper
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
        $repository = $em->getRepository(CommentsEntity::class);
        $comments = $repository->findBy(
            [
                'book' => $book_id
            ]
        );

        return $this->getView()->render('application/comments/list',
            [
                'id' => $book_id,
                'comments' => $comments
            ]
        );

    }


}