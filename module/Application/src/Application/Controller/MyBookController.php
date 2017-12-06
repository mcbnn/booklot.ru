<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 05.12.17
 * Time: 13:32
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity\MyBook;
use Zend\View\Model\ViewModel;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator as ZendPaginator;


class MyBookController  extends AbstractActionController
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em = null;

    protected function getEntityManager()
    {
        if ($this->em == null) {
            $this->em = $this->getServiceLocator()->get(
                'doctrine.entitymanager.orm_default'
            );
        }
        return $this->em;
    }

    public function ListAction()
    {
        $user = $this->getServiceLocator()->get('AuthService')->getIdentity();
        $page = $this->params()->fromRoute('page', 1);
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Repository\MyBookRepository */
        $repository = $em->getRepository(MyBook::class);
        $query = $repository->getMyBookUser($user->id);
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page);
        $vm = new ViewModel(['paginator' => $paginator]);
        return $vm;
    }

}