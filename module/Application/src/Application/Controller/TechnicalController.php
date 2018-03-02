<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Entity\MZhanr;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Sql\Expression;
use Application\Entity\Book;
use Application\Entity\MAvtor;
use Application\Entity\MTranslit;
use Application\Entity\MSerii;

class TechnicalController extends AbstractActionController
{
    public static $text = "";
    public $index = 0;
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
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function countBookAction()
    {
        /** @var  $repository \Application\Repository\BookRepository */
        $em = $this->getEntityManager();
        /** @var  $mzhanrs \Application\Entity\MZhanr */
        $mzhanrs = $this->em->getRepository(MZhanr::class)->findAll(['id' => 534]);
        foreach($mzhanrs as $mzhanr){
            /** @var  $mzhanr \Application\Entity\MZhanr */
            /** @var \Doctrine\ORM\PersistentCollection $books */
            $books = $mzhanr->getBook();
            $mzhanr->setCountBook($books->count());
            $em->persist($mzhanr);
        }
        $em->flush();
    }

    /**
     * @param $arr
     *
     * @return array
     */
    public function checkCountArray($arr)
    {
        if (count($arr) >= 3000) {
            $this->index++;
            $this->insertFileSitemap($arr);
            $arr = array();
        }

        return $arr;
    }

    /**
     * генерация карты sitemap
     */
    public function sitemapAction()
    {
        $config = $this->sm->get('config');
        $em = $this->getEntityManager();
        $site = $config['BASE_URL'];
        chdir('public/sitemap');
        foreach (glob("*.xml") as $filename) {
            unlink($filename);
        }

        //жанры
        $book  = $em->getRepository(Book::class)->getResults();
        foreach ($book as $item){
            $em->detach($item[0]);
            $em->clear();
            $item = $item[0];
            $ar = [];
            $ar['loc'] = $site.$this->sm->get(
                    'ViewHelperManager'
                )->get('url')->__invoke(
                    'home/genre/one/book',
                    [
                        'alias_menu' => $item->getNAliasMenu(),
                        's'          => $item->getNS(),
                        'book'       => $item->getAlias(),
                    ]
                );
            $ar['lastmod'] = date("Y-m-d");
            $ar['changefreq'] = "monthly";
            $ar['priority'] = "1";
            $arr[] = $ar;
            $arr = $this->checkCountArray($arr);
            if($item->getSoder()->count()){
                foreach($item->getSoder()  as $soder){
                    $ar = [];
                    $ar['loc'] = $site.$this->sm->get(
                            'ViewHelperManager'
                        )->get('url')->__invoke(
                            'home/genre/one/book/content',
                            [
                                'alias_menu' => $item->getNAliasMenu(),
                                's'          => $item->getNS(),
                                'book'       => $item->getAlias(),
                                'content'    => $soder->getAlias(),
                            ]
                        );
                    $ar['lastmod'] = date("Y-m-d");
                    $ar['changefreq'] = "never";
                    $ar['priority'] = "0.4";
                    $arr[] = $ar;
                    $arr = $this->checkCountArray($arr);
                }
            }
            if($item->getText()->count()){
                for ($i = 1; $i <=$item->getText()->count(); $i++) {
                    $ar = [];
                    $ar['loc'] = $site.$this->sm->get(
                            'ViewHelperManager'
                        )->get('url')->__invoke(
                            'home/genre/one/book/read',
                            [
                                'alias_menu' => $item->getNAliasMenu(),
                                's'          => $item->getNS(),
                                'book'       => $item->getAlias(),
                                'page_str'   => $i,
                            ]
                        );
                    $ar['lastmod'] = date("Y-m-d");
                    $ar['changefreq'] = "never";
                    $ar['priority'] = "0.4";
                    $arr[] = $ar;
                    $arr = $this->checkCountArray($arr);
                }
            }
        }

        //aвторы
        $avtor  = $em->getRepository(MAvtor::class)->getResults();
        foreach ($avtor as $item){
            $em->detach($item[0]);
            $em->clear();
            $item = $item[0];
            $ar = [];
            $ar['loc'] = $site.$this->sm->get(
                    'ViewHelperManager'
                )->get('url')->__invoke(
                    'home/authors/one',
                    ['alias_menu' => $item->getAlias()]
                );
            $ar['lastmod'] = date("Y-m-d");
            $ar['changefreq'] = "monthly";
            $ar['priority'] = "0.8";
            $arr[] = $ar;
            $arr = $this->checkCountArray($arr);

            if($item->getBooks()->count()){
                foreach($item->getBooks() as $book) {
                    if($book->getVis() == 0)continue;
                    $ar = [];
                    $ar['loc'] = $site.$this->sm->get(
                            'ViewHelperManager'
                        )->get('url')->__invoke(
                            'home/authors/one/book',
                            [
                                'alias_menu' => $item->getAlias(),
                                'book'       => $book->getAlias(),
                            ]
                        );
                    $ar['lastmod'] = date("Y-m-d");
                    $ar['changefreq'] = "never";
                    $ar['priority'] = "0.6";
                    $arr[] = $ar;
                    $arr = $this->checkCountArray($arr);
                    if($book->getSoder()->count()){
                        foreach($book->getSoder()  as $soder){
                            $ar = [];
                            $ar['loc'] = $site.$this->sm->get(
                                    'ViewHelperManager'
                                )->get('url')->__invoke(
                                    'home/authors/one/book/content',
                                    [
                                        'alias_menu' => $item->getAlias(),
                                        'book'       => $book->getAlias(),
                                        'content'    => $soder->getAlias(),
                                    ]
                                );
                            $ar['lastmod'] = date("Y-m-d");
                            $ar['changefreq'] = "never";
                            $ar['priority'] = "0.4";
                            $arr[] = $ar;
                            $arr = $this->checkCountArray($arr);
                        }
                    }
                }
            }
        }

        //переводчик
        $translit = $em->getRepository(MTranslit::class)->getResults();
        foreach ($translit as $item){
            $em->detach($item[0]);
            $em->clear();
            $item = $item[0];
            $ar = [];
            $ar['loc'] = $site.$this->sm->get(
                    'ViewHelperManager'
                )->get('url')->__invoke(
                    'home/translit/one',
                    ['alias_menu' => $item->getAlias()]
                );
            $ar['lastmod'] = date("Y-m-d");
            $ar['changefreq'] = "monthly";
            $ar['priority'] = "0.8";
            $arr[] = $ar;
            $arr = $this->checkCountArray($arr);
            if($item->getBooks()->count()){
                foreach($item->getBooks() as $book) {
                    if($book->getVis() == 0)continue;
                    $ar = [];
                    $ar['loc'] = $site.$this->sm->get(
                            'ViewHelperManager'
                        )->get('url')->__invoke(
                            'home/translit/one/book',
                            [
                                'alias_menu' => $item->getAlias(),
                                'book'       => $book->getAlias(),
                            ]
                        );
                    $ar['lastmod'] = date("Y-m-d");
                    $ar['changefreq'] = "never";
                    $ar['priority'] = "0.6";
                    $arr[] = $ar;
                    $arr = $this->checkCountArray($arr);
                    if($book->getSoder()->count()){
                        foreach($book->getSoder()  as $soder){
                            $ar = [];
                            $ar['loc'] = $site.$this->sm->get(
                                    'ViewHelperManager'
                                )->get('url')->__invoke(
                                    'home/translit/one/book/content',
                                    [
                                        'alias_menu' => $item->getAlias(),
                                        'book'       => $book->getAlias(),
                                        'content'    => $soder->getAlias(),
                                    ]
                                );
                            $ar['lastmod'] = date("Y-m-d");
                            $ar['changefreq'] = "never";
                            $ar['priority'] = "0.4";
                            $arr[] = $ar;
                            $arr = $this->checkCountArray($arr);
                        }
                    }
                }
            }
        }
        //серии
        $serii = $em->getRepository(MSerii::class)->getResults();
        foreach ($serii as $item){
            $em->detach($item[0]);
            $em->clear();
            $item = $item[0];
            $ar = [];
            $ar['loc'] = $site.$this->sm->get(
                    'ViewHelperManager'
                )->get('url')->__invoke(
                    'home/series/one',
                    ['alias_menu' => $item->getAlias()]
                );
            $ar['lastmod'] = date("Y-m-d");
            $ar['changefreq'] = "monthly";
            $ar['priority'] = "0.8";
            $arr[] = $ar;
            $arr = $this->checkCountArray($arr);
            if($item->getBooks()->count()){
                foreach($item->getBooks() as $book) {
                    if($book->getVis() == 0)continue;
                    $ar = [];
                    $ar['loc'] = $site.$this->sm->get(
                            'ViewHelperManager'
                        )->get('url')->__invoke(
                            'home/series/one/book',
                            [
                                'alias_menu' => $item->getAlias(),
                                'book'       => $book->getAlias(),
                            ]
                        );
                    $ar['lastmod'] = date("Y-m-d");
                    $ar['changefreq'] = "never";
                    $ar['priority'] = "0.6";
                    $arr[] = $ar;
                    $arr = $this->checkCountArray($arr);
                    if($book->getSoder()->count()){
                        foreach($book->getSoder()  as $soder){
                            $ar = [];
                            $ar['loc'] = $site.$this->sm->get(
                                    'ViewHelperManager'
                                )->get('url')->__invoke(
                                    'home/series/one/book/content',
                                    [
                                        'alias_menu' => $item->getAlias(),
                                        'book'       => $book->getAlias(),
                                        'content'    => $soder->getAlias(),
                                    ]
                                );
                            $ar['lastmod'] = date("Y-m-d");
                            $ar['changefreq'] = "never";
                            $ar['priority'] = "0.4";
                            $arr[] = $ar;
                            $arr = $this->checkCountArray($arr);
                        }
                    }
                }
            }
        }

        $si = '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $time = date('Y-m-d');
        $count = 0;
        foreach (glob("*.xml") as $k => $filename) {
            $si .= "<sitemap><loc>".$site
                ."/sitemap/$filename</loc><lastmod>$time</lastmod></sitemap>";
            $count++;
        }
        $si .= '</sitemapindex>';
        $r = fopen("sitemap.xml", "w");
        fwrite($r, $si);
        syslog(LOG_INFO,
            json_encode([
                'type' => 'sitemap',
                'date' => date('d.m.Y H:i:s')
            ])
        );
    }

    /**
     * @param $arr
     */
    public function insertFileSitemap($arr)
    {
        $t = '';
        foreach ($arr as $k => $v) {
            $t .= "<url><loc>{$v['loc']}</loc><lastmod>{$v['lastmod']}</lastmod><priority>{$v['priority']}</priority><changefreq>{$v['changefreq']}</changefreq></url>";
        }
        $e = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $e .= $t;
        $e .= '</urlset>';
        $r = fopen("sitemap".$this->index.".xml", "w");
        fwrite($r, $e);
    }
}
