<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 05.12.17
 * Time: 13:32
 */

namespace Application\Controller;

use Zend\ServiceManager\ServiceManager;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Form\AdForm;
use Application\Entity\Ad;
use Application\Entity\AdStat;
use Zend\View\Model\ViewModel;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator as ZendPaginator;

class AdminAdController extends AbstractActionController
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

    public function statAction()
    {
        $em = $this->getEntityManager();
        $id = $this->params()->fromRoute('id');
        $page = $this->params()->fromRoute('page', 1);
        $ad = $em->getRepository(Ad::class)->find($id);
        if(!$ad)return;
        /** @var $repositoryAdStat \Application\Repository\AdStatRepository */
        $repositoryAdStat = $em->getRepository(AdStat::class);
        $query = $repositoryAdStat->getStatAll($id, $this->params()->fromQuery());
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(100);
        $paginator->setCurrentPageNumber($page);
        $graf =  $repositoryAdStat->getDateCount($id);
        return new ViewModel(
            [
                'paginator' => $paginator,
                'ad'     => $ad,
                'graf'   => json_encode($graf),
                'get'    => $this->params()->fromQuery()
            ]
        );
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $em = $this->getEntityManager();
        $ad = $em->getRepository(Ad::class)->findBy([], ['adId' => 'desc']);
        return new ViewModel(
            [
                'ad' => $ad,
            ]
        );
    }

    /**
     * @param string $type
     * @return Response|ViewModel
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addAction($type = 'add')
    {
        $em = $this->getEntityManager();
        if($type == 'edit'){
            $id = $this->params()->fromRoute('id');
            $ad =   $em->getRepository(Ad::class)->find($id);
        }
        else{
            $ad = new Ad();
        }

        $form = new AdForm($em);
        $form->setHydrator(
            new DoctrineObject (
                $em, Ad::class
            )
        );
        $form->bind($ad);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                    $em->persist($ad);
                    $em->flush();
                return $this->redirect()->toRoute(
                    'home/admin-ad', ['action' => 'index']
                );
            }
        }

        return new ViewModel(
            [
                'form' => $form,
            ]
        );
    }

    /**
     * @return Response|ViewModel
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function editAction(){
        return $this->addAction('edit');
    }

}