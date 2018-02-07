<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Sql\Expression;
use Application\Entity\Book;
use Application\Entity\MAvtor;
use Application\Entity\MZhanr;

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

    public function checkAliasBookAction()
    {

        /** @var  $repository \Application\Repository\BookRepository */
        $em = $this->getEntityManager();

        $book_dubles = $em
            ->getRepository(Book::class)
            ->checkAliasBook();

        foreach($book_dubles as $item){
            $dubles = $em->getRepository(Book::class)
                ->findLikeAlias($item->getAlias());

            if(count($dubles) == 1)continue;
            foreach($dubles as $k => $duble){
                var_Dump($duble->getAlias());
                /** @var $duble \Application\Entity\MAvtor */
                if($k == 0){
                    $first = $duble;
                    continue;
                }
                /** @var  $bookFactory \Application\Controller\BookController */
                $bookFactory = $this->sm->get('book');
                $bookFactory->deleteBook($item->getId());
            }
        }
    }

    public function checkAliasAuthorsAction()
    {
        /** @var  $repository \Application\Repository\BookRepository */
        $em = $this->getEntityManager();
        /** @var  $mzhanrs \Application\Entity\MAvtor */
        $authors_dubles = $em
            ->getRepository(MAvtor::class)
            ->checkAliasAuthors();

        foreach($authors_dubles as $item){
            $dubles = $em->getRepository(MAvtor::class)
                ->findLikeAlias($item->getAlias());
            if(count($dubles) == 1)continue;
            foreach($dubles as $k => $duble){
                /** @var $duble \Application\Entity\MAvtor */
                if($k == 0){
                    $first = $duble;
                    continue;
                }
                if($duble->getAvtors()->count() != 0){
                    foreach($duble->getAvtors() as $avtor){
                        /** @var $avtor \Application\Entity\Avtor */
                        $avtor->setIdMenu($first);
                        $em->persist($avtor);
                    }
                }
                $em->remove($duble);
                $em->flush();
            }
        }
        die();

    }

    /**
     * парсинг пока не работает
     */
    public function commentsAction()
    {

        $p = new ParserController;
        $em = $this->getEntityManager();
        if ($p->commentParser($this->sm, $em)) {
            echo 'Парсинг прошел';
        } else {
            echo 'Парсинг не удачен (';
        }
        die();
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
     *
     */
    public function sitemapAction()
    {
        $site = "https://www.booklot.ru";
        chdir('public/sitemap');
        foreach (glob("*.xml") as $filename) {
            unlink($filename);
        }
        $arr = array();
        //переводчик
        $where = "alias != ''";
        $translit = $this->sm->get('Application\Model\MTranslitTable')->fetchAll(
            false,
            false,
            $where
        );
        foreach ($translit as $k => $v) {

            $ar['loc'] = $site.$this->sm->get(
                        'ViewHelperManager'
                    )->get('url')->__invoke(
                        'home/translit/one',
                        ['alias_menu' => $v->alias]
                    );
            $ar['lastmod'] = date("Y-m-d");
            $ar['changefreq'] = "monthly";
            $ar['priority'] = "0.8";
            $arr[] = $ar;
            $arr = $this->checkCountArray($arr);
            $where = "translit.id_menu = '{$v->id}'";
            $mtranslit = $this->sm->get('Application\Model\MTranslitTable')
                ->joinTranslit()->joinBook()->fetchAll(false, false, $where);

            if ($mtranslit->count() != 0) {
                foreach ($mtranslit as $v1) {
                    $ar['loc'] = $site.$this->sm->get(
                            'ViewHelperManager'
                        )->get('url')->__invoke(
                            'home/translit/one/book',
                            [
                                'alias_menu' => $v->alias,
                                'book'       => $v1->book_alias,
                            ]
                        );
                    $ar['lastmod'] = date("Y-m-d");
                    $ar['changefreq'] = "never";
                    $ar['priority'] = "0.6";
                    $arr[] = $ar;
                    $arr = $this->checkCountArray($arr);
                    $where = "soder.id_main = {$v1->book_id}";
                    $soder = $this->sm->get('Application\Model\SoderTable')->fetchAll(
                        false,
                        'id ASC',
                        $where
                    );
                    if ($soder->count() != 0) {
                        foreach ($soder as $v2) {
                            $ar['loc'] = $site.$this->sm->get(
                                    'ViewHelperManager'
                                )->get('url')->__invoke(
                                    'home/translit/one/book/content',
                                    [
                                        'alias_menu' => $v->alias,
                                        'book'       => $v1->book_alias,
                                        'content'    => $v2->alias,
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
        $serii = $this->sm->get('Application\Model\MSeriiTable')->fetchAll(false);
        foreach ($serii as $k => $v) {
            $ar['loc'] = $site.$this->sm->get(
                    'ViewHelperManager'
                )->get('url')->__invoke(
                        'home/series/one',
                        [
                            'alias_menu' => $v->alias,
                        ]
                    );
            $ar['lastmod'] = date("Y-m-d");
            $ar['changefreq'] = "monthly";
            $ar['priority'] = "0.8";
            $arr[] = $ar;
            $arr = $this->checkCountArray($arr);
            $where = "serii.id_menu = '{$v->id}'";
            $mserii = $this->sm->get('Application\Model\MSeriiTable')->joinSerii()
                ->joinBook()->fetchAll(false, false, $where);

            if ($mserii->count() != 0) {
                foreach ($mserii as $v1) {
                    $ar['loc'] = $site.$this->sm->get(
                            'ViewHelperManager'
                        )->get('url')->__invoke(
                            'home/series/one/book',
                            [
                                'alias_menu' => $v->alias,
                                'book'       => $v1->book_alias,
                            ]
                        );
                    $ar['lastmod'] = date("Y-m-d");
                    $ar['changefreq'] = "never";
                    $ar['priority'] = "0.6";
                    $arr[] = $ar;
                    $arr = $this->checkCountArray($arr);
                    $where = "soder.id_main = {$v1->book_id}";
                    $soder = $this->sm->get('Application\Model\SoderTable')->fetchAll(
                        false,
                        'id ASC',
                        $where
                    );
                    if ($soder->count() != 0) {
                        foreach ($soder as $v2) {
                            $ar['loc'] = $site.$this->sm->get(
                                    'ViewHelperManager'
                                )->get('url')->__invoke(
                                    'home/series/one/book/content',
                                    [
                                        'alias_menu' => $v->alias,
                                        'book'       => $v1->book_alias,
                                        'content'    => $v2->alias,
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
        //авторы
        $where = "alias != ''";
        $authors = $this->sm->get('Application\Model\MAvtorTable')->fetchAll(
            false,
            false,
            $where
        );
        foreach ($authors as $k => $v) {

            $ar['loc'] = $site.$this->sm->get(
                    'ViewHelperManager'
                )->get('url')->__invoke(
                        'home/authors/one',
                        [
                            'alias_menu' => $v->alias,
                        ]
                    );
            $ar['lastmod'] = date("Y-m-d");
            $ar['changefreq'] = "monthly";
            $ar['priority'] = "1";
            $arr[] = $ar;
            $arr = $this->checkCountArray($arr);
            $where = "avtor.id_menu = '{$v->id}'";
            $avtor = $this->sm->get('Application\Model\MAvtorTable')->joinAvtor()
                ->joinBook()->fetchAll(false, false, $where);

            if ($avtor->count() != 0) {
                foreach ($avtor as $v1) {
                    $ar['loc'] = $site.$this->sm->get(
                            'ViewHelperManager'
                        )->get('url')->__invoke(
                            'home/authors/one/book',
                            [
                                'alias_menu' => $v->alias,
                                'book'       => $v1->book_alias,
                            ]
                        );
                    $ar['lastmod'] = date("Y-m-d");
                    $ar['changefreq'] = "never";
                    $ar['priority'] = "0.8";
                    $arr[] = $ar;
                    $arr = $this->checkCountArray($arr);
                    $where = "soder.id_main = {$v1->book_id}";
                    $soder = $this->sm->get('Application\Model\SoderTable')->fetchAll(
                        false,
                        'id ASC',
                        $where
                    );
                    if ($soder->count() != 0) {
                        foreach ($soder as $v2) {
                            $ar['loc'] = $site.$this->sm->get(
                                    'ViewHelperManager'
                                )->get('url')->__invoke(
                                    'home/authors/one/book/content',
                                    [
                                        'alias_menu' => $v->alias,
                                        'book'       => $v1->book_alias,
                                        'content'    => $v2->alias,
                                    ]
                                );
                            $ar['lastmod'] = date("Y-m-d");
                            $ar['changefreq'] = "never";
                            $ar['priority'] = "0.6";
                            $arr[] = $ar;
                            $arr = $this->checkCountArray($arr);
                        }
                    }
                }
            }

        }

        //жанры
        $order = 'book.id ASC';
        $where = 'book.vis = 1 and menu_id is not null';
        $book = $this->sm->get('Application\Model\BookTable')
            ->joinColumn(
                [
                    new Expression('distinct book.id as id'),
                    'alias',
                    'n_alias_menu',
                    'name_zhanr',
                    'n_s',
                ]
            )
            ->fetchAll(false, $order, $where);


        foreach ($book as $k => $v) {
            $v = (array)$v;
            $ar['loc'] = $site.$this->sm->get(
                    'ViewHelperManager'
                )->get('url')->__invoke(
                        'home/genre/one/book',
                        [
                            'alias_menu' => $v['n_alias_menu'],
                            's'          => $v['n_s'],
                            'book'       => $v['alias'],
                        ]
                    );
            $ar['lastmod'] = date("Y-m-d");
            $ar['changefreq'] = "monthly";
            $ar['priority'] = "1";
            $arr[] = $ar;
            $arr = $this->checkCountArray($arr);
            $where = "soder.id_main = {$v['id']}";
            $soder = $this->sm->get('Application\Model\SoderTable')->fetchAll(
                false,
                'id ASC',
                $where
            );
            if ($soder->count() != 0) {
                foreach ($soder as $v1) {
                    $ar['loc'] = $site.$this->sm->get(
                            'ViewHelperManager'
                        )->get('url')->__invoke(
                            'home/genre/one/book/content',
                            [
                                'alias_menu' => $v['n_alias_menu'],
                                's'          => $v['n_s'],
                                'book'       => $v['alias'],
                                'content'    => $v1->alias,
                            ]
                        );
                    $ar['lastmod'] = date("Y-m-d");
                    $ar['changefreq'] = "never";
                    $ar['priority'] = "0.8";
                    $arr[] = $ar;
                    $arr = $this->checkCountArray($arr);
                }
            }
            $where = "text_dop.id_main = {$v['id']}";
            $text = $this->sm->get('Application\Model\TextDopTable')->fetchAll(
                false,
                'id ASC',
                $where
            );
            if ($text->count() != 0) {
                for ($i = 1; $i <= $text->count(); $i++) {
                    $ar['loc'] = $site.$this->sm->get(
                            'ViewHelperManager'
                        )->get('url')->__invoke(
                            'home/genre/one/book/read',
                            [
                                'alias_menu' => $v['n_alias_menu'],
                                's'          => $v['n_s'],
                                'book'       => $v['alias'],
                                'page_str'   => $i,
                            ]
                        );
                    $ar['lastmod'] = date("Y-m-d");
                    $ar['changefreq'] = "never";
                    $ar['priority'] = "0.8";
                    $arr[] = $ar;
                    $arr = $this->checkCountArray($arr);
                }
            }
        }
        $si = '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $time = date('Y-m-d');
        foreach (glob("*.xml") as $filename) {
            $si .= "<sitemap><loc>".$site
                ."/sitemap/$filename</loc><lastmod>$time</lastmod></sitemap>";
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
        $time = time();
        $time = date('Y-m-d', $time);
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
