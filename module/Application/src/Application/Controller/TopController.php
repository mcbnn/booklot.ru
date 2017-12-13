<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 05.12.17
 * Time: 13:32
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Form\RegForm;
use Application\Entity\MZhanr;
use Application\Entity\Book;
use Zend\View\Model\ViewModel;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator as ZendPaginator;


class TopController extends AbstractActionController
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

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $em = $this->getEntityManager();
        $get = $this->params()->fromQuery();
        $repository = $em->getRepository(MZhanr::class);
        $mzhanr = $repository->getChild($get);

        return new ViewModel(
            [
                'mzhanr' => $mzhanr,
                'get'      => $get,
            ]
        );
    }

    /**
     * @return void|ViewModel
     */
    public function topAction()
    {
        $alias = $this->params()->fromRoute('alias', false);
        if (!$alias) {
            return;
        }
        $em = $this->getEntityManager();

        $repository = $em->getRepository(Book::class);
        $books = $repository->getBoksOneZhanr($alias);

        $mzhanr = $em->getRepository(MZhanr::class)->findOneBy(
            [
                'alias' => $alias,
            ]
        );

        return new ViewModel(
            [
                'books'  => $books,
                'mzhanr' => $mzhanr,
            ]
        );
    }
}