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
use Application\Entity\Articles;
use Zend\View\Model\ViewModel;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator as ZendPaginator;

class ArticlesController  extends AbstractActionController
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
    public function indexAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        $em = $this->getEntityManager();
        $get = $this->params()->fromQuery();
        /** @var  $repository \Application\Repository\ArticlesRepository */
        $repository = $em->getRepository(Articles::class);
        $query = $repository->getArticlesAll($get);
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(2);
        $paginator->setCurrentPageNumber($page);

        $vm = new ViewModel(
            [
                'paginator' => $paginator
            ]
        );

        return $vm;
    }

    /**
     * @return ViewModel|null
     */
    public function articleAction(){

        $alias = $this->params()->fromRoute('alias', false);
        if(!$alias)return null;
        $em = $this->getEntityManager();

        $repository = $em->getRepository(Articles::class);
        $article = $repository->findOneBy(['alias' => $alias]);

        $this->seo(
            $article->getTitle(),
            $article->getTitle()
        );

        $vm = new ViewModel(
            [
                'article' => $article
            ]
        );

        return $vm;
    }

    /**
     * @param        $name
     * @param string $title
     * @param string $discription
     * @param string $keywords
     */
    public function seo($name, $title = "", $discription = "", $keywords = "")
    {
        $title = (empty($title)) ? $name : $title;
        $discription = (empty($discription)) ? $title : $discription;
        $keywords = (empty($keywords)) ? $title : $keywords;
        $renderer = $this->sm->get(
            'Zend\View\Renderer\PhpRenderer'
        );
        $renderer->headTitle($title);
        $renderer->headMeta()->appendName('description', $discription);
        $renderer->headMeta()->appendName('keywords', $keywords);

    }
}