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
        $cache = $em->getConfiguration()->getResultCacheImpl();
        $get = $this->params()->fromQuery();

        $cacheItemKey = 'top_index_'.md5(implode($get));
        if ($cache->contains($cacheItemKey)) {
            $item = $cache->fetch($cacheItemKey);
        } else {
            $repository = $em->getRepository(MZhanr::class);
            $item = $repository->getChild($get['name_zhanr']);
            $cache->save($cacheItemKey, $item);
        };
        $this->seo(
            "Топ 10 книг, разных жанров",
            "Топ 10 книг, разных жанров"
        );

        return new ViewModel(
            [
                'mzhanr' => $item,
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

        $this->seo(
            "Топ 10 книг \"".$mzhanr->getName()."\"",
            "Топ 10 книг \"".$mzhanr->getName()."\""
        );


        return new ViewModel(
            [
                'books'  => $books,
                'mzhanr' => $mzhanr,
            ]
        );
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
        $renderer = $this->getServiceLocator()->get(
            'Zend\View\Renderer\PhpRenderer'
        );
        $renderer->headTitle($title);
        $renderer->headMeta()->appendName('description', $discription);
        $renderer->headMeta()->appendName('keywords', $keywords);

    }
}