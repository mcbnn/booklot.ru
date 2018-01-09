<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 05.12.17
 * Time: 13:32
 */

namespace Application\Controller;

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity\MyBookStatus;
use Application\Entity\MyBookStatusName;
use Zend\View\Model\ViewModel;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator as ZendPaginator;

class MyBookStatusController  extends AbstractActionController
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em = null;

    /**
     * @var null|ServiceManager
     */
    public $sm = null;

    public function __construct(ServiceManager $servicemanager)
    {
        $this->sm = $servicemanager;
    }

    /**
     * @return array|\Doctrine\ORM\EntityManager|object
     */
    protected function getEntityManager()
    {
        if ($this->em == null) {
            $this->em = $this->sm->get(
                'doctrine.entitymanager.orm_default'
            );
        }
        return $this->em;
    }

    /**
     * @return ViewModel
     */
    public function ListAction()
    {
        $user = $this->sm->get('AuthService')->getIdentity();
        $page = $this->params()->fromRoute('page', 1);
        $get = $this->params()->fromQuery();
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Repository\MyBookStatusRepository */
        $repository = $em->getRepository(MyBookStatus::class);
        $query = $repository->getMyBookStatusUser($user->id, $get);
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(100);
        $paginator->setCurrentPageNumber($page);

        $status_all = $em->getRepository(MyBookStatusName::class)->findAll();
        $vm = new ViewModel(
            [
                'paginator'  => $paginator,
                'status_all' => $status_all,
                'get'        => $get,
            ]
        );
        return $vm;
    }

}