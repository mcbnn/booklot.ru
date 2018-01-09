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
use Application\Entity\MZhanr;
use Application\Entity\Book;
use Zend\View\Model\ViewModel;


class TopController extends AbstractActionController
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
        $em = $this->getEntityManager();
        $cache = $em->getConfiguration()->getResultCacheImpl();
        $get = $this->params()->fromQuery();
        $cacheItemKey = 'top_index_'.md5(implode($get));
        if ($cache->contains($cacheItemKey)) {
            $item = $cache->fetch($cacheItemKey);
        } else {
            /** @var  $repository \Application\Repository\MZhanrRepository */
            $repository = $em->getRepository(MZhanr::class);
            $item = $repository->getChild(($get['name_zhanr'])??null);
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
     * @return null|ViewModel
     */
    public function topAction()
    {
        $alias = $this->params()->fromRoute('alias', false);
        if (!$alias)return null;
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Repository\BookRepository */
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
        $renderer = $this->sm->get(
            'Zend\View\Renderer\PhpRenderer'
        );
        $renderer->headTitle($title);
        $renderer->headMeta()->appendName('description', $discription);
        $renderer->headMeta()->appendName('keywords', $keywords);

    }
}