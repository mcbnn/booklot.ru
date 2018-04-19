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
use Application\Entity\Serii;
use Application\Entity\Avtor;
use Application\Entity\Translit;

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

    public function dubletranslitAction()
    {
        /** @var  $repository \Application\Repository\BookRepository */
        $em = $this->getEntityManager();
        /** @var  $translits \Application\Entity\MTranslit */
        $translits = $this->em->getRepository(MTranslit::class)->getDubleAlias();
        $mainController = new MainController();
        foreach($translits as $item){
            $alias = $item['alias'];

            $translit_duble = $this->em->getRepository(MTranslit::class)->findBy(['alias' => $alias], ['id' => 'ASC']);
            if(count($translit_duble) < 2)continue;

            $first = null;
            var_dump(count($translit_duble));
            var_dump($alias);
            foreach ($translit_duble as $k => $v){
                if($k == 0){
                    $name_change = $v->getName();
                    $v->setAlias($v->getId().'-'. $mainController->trans($name_change));
                    $em->persist($v);
                    var_dump($v->getAlias());
                    $first =  $v;
                    continue;
                };

                if($first == null)continue;
                $translit_id = $v->getId();
                $translit = $this->em->getRepository(Translit::class)
                    ->findBy(['idMenu' => $translit_id]);
                if(count($translit) == 0){
                    $em->remove($v);
                    continue;

                };
                foreach($translit as $v2){
                    /** @var $v2 \Application\Entity\Translit */
                    $v2->setIdMenu($first);
                    $em->persist($v2);

                }
                $em->remove($v);
            }
            $em->flush();
        }

        die();
    }


    public function dubleavtorAction()
    {
        /** @var  $repository \Application\Repository\BookRepository */
        $em = $this->getEntityManager();
        /** @var  $avtors \Application\Entity\MAvtor */
        $avtors = $this->em->getRepository(MAvtor::class)->getDubleAlias();
        $mainController = new MainController();
        foreach($avtors as $item){
            $alias = $item['alias'];

            $avtor_duble = $this->em->getRepository(MAvtor::class)->findBy(['alias' => $alias], ['id' => 'ASC']);
            if(count($avtor_duble) < 2)continue;

            $first = null;
            var_dump(count($avtor_duble));
            var_dump($alias);
            foreach ($avtor_duble as $k => $v){
                if($k == 0){
                    $name_change = $v->getName();
                    $v->setAlias($v->getId().'-'. $mainController->trans($name_change));
                    $em->persist($v);
                    var_dump($v->getAlias());
                    $first =  $v;
                    continue;
                };

                if($first == null)continue;
                $avtor_id = $v->getId();
                $avtor = $this->em->getRepository(Avtor::class)
                    ->findBy(['idMenu' => $avtor_id]);
                if(count($avtor) == 0){
                    $em->remove($v);
                    continue;

                };
                foreach($avtor as $v2){
                    /** @var $v2 \Application\Entity\Avtor */
                    $v2->setIdMenu($first);
                    $em->persist($v2);

                }
                $em->remove($v);
            }
            $em->flush();
        }

        die();
    }


    public function dublealiasAction()
    {
        /** @var  $repository \Application\Repository\BookRepository */
        $em = $this->getEntityManager();
        /** @var  $book \Application\Entity\Book */
        $books = $this->em->getRepository(Book::class)->getDubleAlias();
        foreach($books as $book){
            $alias = $book['alias'];
            var_dump($alias);
            $book_duble = $this->em->getRepository(Book::class)->findBy(['alias' => $alias], ['id' => 'ASC']);
            if(count($book_duble) < 2)continue;
            foreach ($book_duble as $k => $v){
               if($k == 0 and $v->getFoto() != 'nofoto.jpg')continue;
                $bookFactory = $this->sm->get('book');
                $bookFactory->deleteBook($v->getId());
            }
        }

        die();
    }

    public function seriesAction()
    {
        /** @var  $repository \Application\Repository\BookRepository */
        $em = $this->getEntityManager();
        $mainController = new MainController();

        /** @var  $mserii \Application\Entity\MSerii */
        $mserii = $this->em->getRepository(MSerii::class)->findAll();
        foreach($mserii as $item){
            $name = $item->getName();
            if($check = stristr($name, '#', true)){
                $name = trim($check);
                $serii_one = $this->em->getRepository(MSerii::class)
                    ->getResultLike($name);
                var_dump(count($serii_one));
                var_dump($name);
                if(count($serii_one) == 0){
                    continue;
                }
                elseif(count($serii_one) == 1){
                    foreach($serii_one as $k => $v){
                        $name_change = $v->getName();
                        if($name_change = stristr($name_change, '#', true)){
                            $name_change = trim($name_change);
                        }
                        else{
                            $name_change = trim($v->getName());
                        }
                        $v->setName($name_change);
                        $v->setAlias($v->getId().'-'. $mainController->trans($name_change));
                        $em->persist($v);
                    }
                    $em->flush();
                    continue;
                };
                $first = null;
                foreach($serii_one as $k => $v){
                    if($k == 0){
                        $first = $v;
                        $name_change = $v->getName();
                        if($name_change = stristr($name_change, '#', true)){
                            $name_change = trim($name_change);
                        }
                        else{
                            $name_change = trim($v->getName());
                        }

                        $first->setName($name_change);

                        $first->setAlias($first->getId().'-'. $mainController->trans($name_change));
                        $em->persist($first);
                        continue;
                    }
                    if($first == null)continue;
                    $serii_id = $v->getId();
                    $serii = $this->em->getRepository(Serii::class)
                        ->findBy(['idMenu' => $serii_id]);
                    if(count($serii) == 0){
                        $em->remove($v);
                        continue;

                    };
                    foreach($serii as $v2){
                        /** @var $v2 \Application\Entity\Serii */
                        $v2->setIdMenu($first);
                        $em->persist($v2);

                    }
                    $em->remove($v);
                }
                $em->flush();
            }
            continue;
        }
    }

    /**
     * Добавляем жанр к книге
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function nullMenuAction()
    {
        /** @var  $repository \Application\Repository\BookRepository */
        $em = $this->getEntityManager();
        /** @var  $book \Application\Entity\Book */
        $books = $this->em->getRepository(Book::class)->getMenuNull();
        foreach($books as $book){
            $menu = $this->em->getRepository(MZhanr::class)->findOneBy(['alias' => $book->getNAliasMenu()]);
            if(count($menu) == 0)continue;
            $book->setMenu($menu);
            $em->persist($book);
            $em->flush();
        }
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
        if (count($arr) >= 49000) {
            $this->index++;
            $this->insertFileSitemap($arr);
            $arr = array();
        }

        return $arr;
    }

    public function checkfotoAction()
    {
        $config = $this->sm->get('config');
        $em = $this->getEntityManager();
        $book  = $em->getRepository(Book::class)->getResults();
        foreach($book as $item){
            $em->detach($item[0]);
            $em->clear();
            $item = $item[0];
            if($item->getFoto() == 'nofoto.jpg')continue;
            $src = $config['UPLOAD_DIR'].'newimg/original/'.$item->getFoto();
            if(!file_exists($src)){
                $src_full = $config['UPLOAD_DIR'].'newimg/full/'.$item->getFoto();
                $src_small = $config['UPLOAD_DIR'].'newimg/small/'.$item->getFoto();

                if(file_exists($src_full)){
                   copy($src_full, $config['UPLOAD_DIR'].'newimg/original/'.$item->getFoto());
                    var_dump('Фото добавлено full: '.$item->getId().$item->getFoto());
                }
                elseif(file_exists($src_small)){
                    copy($src_small, $config['UPLOAD_DIR'].'newimg/original/'.$item->getFoto());
                    copy($src_small, $config['UPLOAD_DIR'].'newimg/full/'.$item->getFoto());
                    var_dump('Фото добавлено small: '.$item->getId().$item->getFoto());
                }
                elseif(!file_exists($src)){
                    var_dump($item);
                    $item->setFoto('nofoto.jpg');
                    $em->persist($item);
                    $em->flush();
                    var_dump('Фото измененно на nofoto: '.$item->getId().$item->getFoto());
                }
                else{
                    var_dump('Фото уже есть: '.$item->getId().$item->getFoto());
                }
            }
            else{
                var_dump('Фото есть: '.$item->getId().$item->getFoto());
            }

        }
        die();
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
