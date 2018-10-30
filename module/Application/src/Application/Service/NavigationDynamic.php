<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Service;

use Interop\Container\ContainerInterface;
use Zend\Navigation\Service\DefaultNavigationFactory;
use Zend\Db\Sql\Expression;
use Application\Entity\Book;
use Application\Entity\MAvtor;
use Application\Entity\MSerii;
use Application\Entity\MTranslit;
use Application\Entity\MZhanr;

class NavigationDynamic extends DefaultNavigationFactory {

    /**
     * @var bool
     */
    protected $sm = null;

    protected $em = null;

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
     * @param ContainerInterface $container
     *
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function getPages(ContainerInterface $container) {
        /** @var \Zend\ServiceManager\ServiceManager $sm */
        $this->sm = $container->get('ServiceManager');
        $serviceLocator = $this->sm;
        $em = $this->getEntityManager();
        if (null === $this->pages) {
            $application = $serviceLocator->get('Application');
            $routeMatch = $application->getMvcEvent()->getRouteMatch();
            $router = $application->getMvcEvent()->getRouter();
            $fetchMenuObject = $em->getRepository(MZhanr::class)
                ->findBy(['see' => 1], ['idMain' => 'asc', 'id' => 'asc']);
            $book = false;
            $pageBookCount = 0;
            $soder = [];
            $avtor = [];
            $serii = [];
            $translit = [];
            if (!empty($routeMatch)) {
                if ($routeMatch->getMatchedRouteName() == 'home/genre/one/book' or $routeMatch->getMatchedRouteName() == 'home/genre/one/book/read' or $routeMatch->getMatchedRouteName() == 'home/genre/one/book/content') {
                    $bookAlias = strtolower($routeMatch->getParam('book'));
                    $BookObject = $em->getRepository(Book::class)->findOneByRep(['alias' => $bookAlias, 'vis' => 1]);
                    if ($BookObject) {
                        $book = $BookObject;
                        $pageBookCount = $BookObject->getText()->count();
                        $soder = $BookObject->getSoder();
                    }
                }
                elseif ($routeMatch->getMatchedRouteName() == 'home/authors/one' or
                    $routeMatch->getMatchedRouteName() == 'home/authors/one/book' or
                    $routeMatch->getMatchedRouteName() == 'home/authors/one/book/read' or
                    $routeMatch->getMatchedRouteName() == 'home/authors/one/book/content')
                {
                    $alias_author = strtolower($routeMatch->getParam('alias_menu'));
                    $avtor =  $em->getRepository(MAvtor::class)->findOneBy(['alias' => $alias_author]);
                    if ($routeMatch->getMatchedRouteName() == 'home/authors/one/book' or
                        $routeMatch->getMatchedRouteName() == 'home/authors/one/book/read' or
                        $routeMatch->getMatchedRouteName() == 'home/authors/one/book/content') {
                        $bookAlias = strtolower($routeMatch->getParam('book'));
                        $BookObject = $em->getRepository(Book::class)
                            ->findOneByRep(['alias' => $bookAlias, 'vis' => 1]);
                        if ($BookObject) {
                            $book = $BookObject;
                            $pageBookCount = $BookObject->getText()->count();
                            $soder = $BookObject->getSoder();
                        }
                    }
                }
                elseif ($routeMatch->getMatchedRouteName() == 'home/series/one' or
                    $routeMatch->getMatchedRouteName() == 'home/series/one/book' or
                    $routeMatch->getMatchedRouteName() == 'home/series/one/book/read' or
                    $routeMatch->getMatchedRouteName() == 'home/series/one/book/content') {
                    $alias_series = strtolower($routeMatch->getParam('alias_menu'));
                    $serii =  $em->getRepository(MSerii::class)->findOneBy(['alias' => $alias_series]);
                    if ($routeMatch->getMatchedRouteName() == 'home/series/one/book' or
                        $routeMatch->getMatchedRouteName() == 'home/series/one/book/read' or
                        $routeMatch->getMatchedRouteName() == 'home/series/one/book/content'
                    ) {
                        $bookAlias = strtolower($routeMatch->getParam('book'));
                        $BookObject = $em->getRepository(Book::class)
                            ->findOneByRep(['alias' => $bookAlias, 'vis' => 1]);
                        if ($BookObject) {
                            $book = $BookObject;
                            $pageBookCount = $BookObject->getText()->count();
                            $soder = $BookObject->getSoder();
                        }
                    }
                }
                elseif ($routeMatch->getMatchedRouteName() == 'home/translit/one' or
                    $routeMatch->getMatchedRouteName() == 'home/translit/one/book' or
                    $routeMatch->getMatchedRouteName() == 'home/translit/one/book/read' or
                    $routeMatch->getMatchedRouteName() == 'home/translit/one/book/content'
                ) {
                    $alias_translit = strtolower($routeMatch->getParam('alias_menu'));
                    $translit = $em->getRepository(MTranslit::class)->findOneBy(['alias' => $alias_translit]);
                    if ($routeMatch->getMatchedRouteName() == 'home/translit/one/book' or
                        $routeMatch->getMatchedRouteName() == 'home/translit/one/book/read' or
                        $routeMatch->getMatchedRouteName() == 'home/translit/one/book/content'
                    ) {
                        $bookAlias = strtolower($routeMatch->getParam('book'));
                        $BookObject = $em->getRepository(Book::class)
                            ->findOneByRep(['alias' => $bookAlias, 'vis' => 1]);
                        if ($BookObject) {
                            $book = $BookObject;
                            $pageBookCount = $BookObject->getText()->count();
                            $soder = $BookObject->getSoder();
                        }
                    }
                }

            }
            $min = $fetchMenuObject[0];
            $menu = $this->genMenu($fetchMenuObject, $min->getIdMain(), 0, $book, $pageBookCount, $soder, $avtor, $serii, $translit);
            $pages = $this->getPagesFromConfig($menu);

            $this->pages = $this->injectComponents($pages, $routeMatch, $router);
        }
        return $this->pages;
    }

    protected function genMenu($fetchMenuArray, $menu = 0, $parent = 0, $book, $pageBookCount = 0, $soder = 0, $avtor = 0, $serii = 0, $translit = 0) {
        $arr = false;
        foreach ($fetchMenuArray as $v) {
            $v->setRoute(str_replace('/slash','',  $v->getRoute()));
            if ($v->getIdMain() == $menu) {
                $ar = [];
                $ar['route'] = $v->getRoute();
                if($v->getCountBook() == 0){
                    $ar['label'] = $v->getName();
                }
                else{
                    $ar['label'] = $v->getName().' '.$v->getCountBook();
                }
                $ar['class'] = $v->getIcon();
                $ar['label_eng'] = $v->getAlias();
                $ar['vis'] = $v->getVis();
                $arr[  $v->getAlias() ] = $ar;
                if ($v->getAction()) {
                    $ar['action'] = $v->getAction();
                }
                $t = $this->genMenu($fetchMenuArray, $ar, $v->getId(), $book, $pageBookCount, $soder, $avtor, $serii, $translit);
                if (!empty($t)) $arr[ $v->getAlias() ]['pages'] = $t;
            }
            elseif ($v->getIdMain() == $parent) {
                $ar = [];
                $ar['route'] = $v->getRoute();
                if($v->getCountBook() == 0){
                    $ar['label'] = $v->getName();
                }
                else{
                    $ar['label'] = $v->getName().' '.$v->getCountBook();
                }
                $ar['class'] = $v->getIcon();
                $ar['label_eng'] = $v->getAlias();
                $ar['vis'] = $v->getVis();
                if ($v->getVis() and !empty($menu['s'])) {
                    $ar['params']['s'] = $menu['s'];
                    $ar['params']['alias_menu'] = $v->getAlias();
                    $ar['s'] = $menu['s'] . '/' . $v->getAlias();
                }
                elseif ($v->getVis() and empty($menu['s'])) {
                    $ar['s'] = $v->getAlias();
                    $ar['params']['alias_menu'] = $v->getAlias();
                }
                if ($v->getAction()) {
                    $ar['action'] = $v->getAction();
                }
                $arr[ $v->getAlias() ] = $ar;
                $t = $this->genMenu($fetchMenuArray, $ar, $v->getId(), $book, $pageBookCount, $soder, $avtor, $serii, $translit);
                if (!empty($t)) $arr[ $v->getAlias()]['pages'] = $t;

                if ($serii) {
                    if ($v->getRoute() == 'home/series') {
                        $ar = [];
                        $ar['route'] = 'home/series/one';
                        $ar['label'] = $serii->getName();
                        $ar['class'] = 'entypo-user';
                        $ar['label_eng'] = $serii->getAlias();
                        $ar['vis'] = 1;
                        $ar['params'] = [
                            'alias_menu' => $serii->getAlias(),
                        ];
                        $arr[ $v->getAlias() ]['pages'][ $serii->getAlias() ] = $ar;
                        foreach ($serii->getBooks() as $v1) {
                            $ar = [];
                            $ar['route'] = 'home/series/one/book';
                            $ar['label'] = $v1->getName();
                            $ar['class'] = 'entypo-book';
                            $ar['label_eng'] = $v1->getAlias();
                            $ar['vis'] = 1;
                            $ar['params'] = [
                                'alias_menu' => $serii->getAlias(),
                                'book'       => $v1->getAlias(),
                            ];
                            $arr[ $v->getAlias() ]['pages'][ $serii->getAlias() ]['pages'][ $ar['label_eng'] ] = $ar;
                        }
                        if ($pageBookCount != 0) {
                            $arr2 = [];
                            for ($i = 1; $i <= $pageBookCount; $i++) {
                                $ar1 = [];
                                $ar1['route'] = 'home/series/one/book/read';
                                $ar1['label'] = "Страница " . $i;
                                $ar1['label_eng'] = "Page_" . $i;
                                $ar1['vis'] = 1;
                                $ar1['class'] = 'entypo-feather';

                                $ar1['params'] = [
                                    'book'       => $book->getAlias(),
                                    'alias_menu' => $serii->getAlias(),
                                    'page_str'   => $i
                                ];
                                $arr1 = [];
                                $arr1['route'] = 'home/series/one/book/read';
                                $arr1['label'] = "Страницы";
                                $arr1['label_eng'] = "Pages_str";
                                $arr1['vis'] = 1;
                                $arr1['params'] = [
                                    'book'       => $book->getAlias(),
                                    'alias_menu' => $serii->getAlias(),
                                    'page_str'   => ""
                                ];
                                $arr2[ $ar1['label_eng'] ] = $ar1;
                            }
                            $arr[ $v->getAlias() ]['pages'][ $serii->getAlias() ]['pages'][ $book->getAlias() ]['pages'][ $arr1['label_eng'] ] = $arr1;
                            $arr[ $v->getAlias() ]['pages'][ $serii->getAlias() ]['pages'][ $book->getAlias()]['pages'][ $arr1['label_eng'] ]['pages'] = $arr2;
                        }
                        if (count($soder) != 0) {
                            $arr2 = [];
                            foreach ($soder as $v1) {
                                $ar1 = [];
                                $ar1['route'] = 'home/series/one/book/content';
                                $ar1['label'] = $v1->getName();
                                $ar1['label_eng'] = $v1->getAlias();
                                $ar1['vis'] = 1;
                                $ar1['class'] = 'entypo-feather';
                                $ar1['params'] = [
                                    'book'       => $book->getAlias(),
                                    'alias_menu' => $serii->getAlias(),
                                    'content'    => $v1->getAlias()
                                ];
                                $arr1 = [];
                                $arr1['route'] = 'home/series/one/book/content';
                                $arr1['label'] = "Содержание";
                                $arr1['label_eng'] = "Contents_str";
                                $arr1['vis'] = 1;

                                $arr1['params'] = [
                                    'book'       => $book->getAlias(),
                                    'alias_menu' => $serii->getAlias(),
                                    'content'    => ""
                                ];
                                $arr2[ $ar1['label_eng'] ] = $ar1;
                            }

                            $arr[ $v->getAlias() ]['pages'][ $serii->getAlias() ]['pages'][ $book->getAlias() ]['pages'][ $arr1['label_eng'] ] = $arr1;
                            $arr[ $v->getAlias() ]['pages'][ $serii->getAlias() ]['pages'][ $book->getAlias() ]['pages'][ $arr1['label_eng'] ]['pages'] = $arr2;
                        }
                    }
                }
                if ($translit) {
                    if ($v->getRoute() == 'home/translit') {
                        $ar = [];
                        $ar['route'] = 'home/translit/one';
                        $ar['label'] = $translit->getName();
                        $ar['class'] = 'entypo-user';
                        $ar['label_eng'] = $translit->getAlias();
                        $ar['vis'] = 1;
                        $ar['params'] = [
                            'alias_menu' => $translit->getAlias(),
                        ];
                        $arr[ $v->getAlias() ]['pages'][ $translit->getAlias() ] = $ar;
                        foreach ($translit->getBooks() as $v1) {
                            $ar = [];
                            $ar['route'] = 'home/translit/one/book';
                            $ar['label'] = $v1->getName();
                            $ar['class'] = 'entypo-book';
                            $ar['label_eng'] = $v1->getAlias();
                            $ar['vis'] = 1;
                            $ar['params'] = [
                                'alias_menu' => $translit->getAlias(),
                                'book'       => $v1->getAlias(),
                            ];
                            $arr[ $v->getAlias()]['pages'][ $translit->getAlias() ]['pages'][ $ar['label_eng'] ] = $ar;
                        }
                        if ($pageBookCount != 0) {
                            $arr2 = [];
                            for ($i = 1; $i <= $pageBookCount; $i++) {
                                $ar1 = [];
                                $ar1['route'] = 'home/translit/one/book/read';
                                $ar1['label'] = "Страница " . $i;
                                $ar1['label_eng'] = "Page_" . $i;
                                $ar1['vis'] = 1;
                                $ar1['class'] = 'entypo-feather';

                                $ar1['params'] = [
                                    'book'       => $book->getAlias(),
                                    'alias_menu' => $translit->getAlias() ,
                                    'page_str'   => $i
                                ];
                                $arr1 = [];
                                $arr1['route'] = 'home/translit/one/book/read';
                                $arr1['label'] = "Страницы";
                                $arr1['label_eng'] = "Pages_str";
                                $arr1['vis'] = 1;

                                $arr1['params'] = [
                                    'book'       => $book->getAlias(),
                                    'alias_menu' => $translit->getAlias(),
                                    'page_str'   => ""
                                ];
                                $arr2[ $ar1['label_eng'] ] = $ar1;
                            }
                            $arr[ $v->getAlias() ]['pages'][ $translit->getAlias() ]['pages'][ $book->getAlias() ]['pages'][ $arr1['label_eng'] ] = $arr1;
                            $arr[ $v->getAlias() ]['pages'][ $translit->getAlias()]['pages'][ $book->getAlias() ]['pages'][ $arr1['label_eng'] ]['pages'] = $arr2;
                        }
                        if (count($soder) != 0) {
                            $arr2 = [];
                            foreach ($soder as $v1) {
                                $ar1 = [];
                                $ar1['route'] = 'home/translit/one/book/content';
                                $ar1['label'] = $v1->getName();
                                $ar1['label_eng'] = $v1->getAlias();;
                                $ar1['vis'] = 1;
                                $ar1['class'] = 'entypo-feather';
                                $ar1['params'] = [
                                    'book'       => $book->getAlias(),
                                    'alias_menu' => $translit->getAlias(),
                                    'content'    => $v1->getAlias()
                                ];

                                $arr1 = [];
                                $arr1['route'] = 'home/translit/one/book/content';
                                $arr1['label'] = "Содержание";
                                $arr1['label_eng'] = "Contents_str";
                                $arr1['vis'] = 1;
                                $arr1['params'] = [
                                    'book'       => $book->getAlias(),
                                    'alias_menu' => $translit->getAlias() ,
                                    'content'    => ""
                                ];
                                $arr2[ $ar1['label_eng'] ] = $ar1;
                            }
                            $arr[ $v->getAlias() ]['pages'][ $translit->getAlias() ]['pages'][ $book->getAlias() ]['pages'][ $arr1['label_eng'] ] = $arr1;
                            $arr[ $v->getAlias() ]['pages'][ $translit->getAlias() ]['pages'][ $book->getAlias() ]['pages'][ $arr1['label_eng'] ]['pages'] = $arr2;
                        }
                    }
                }

                if ($avtor) {
                    if ($v->getRoute() == 'home/authors') {
                        $ar = [];
                        $ar['route'] = 'home/authors/one';
                        $ar['label'] = $avtor->getName();
                        $ar['class'] = 'entypo-user';
                        $ar['label_eng'] = $avtor->getAlias();
                        $ar['vis'] = 1;
                        $ar['params'] = [
                            'alias_menu' => $avtor->getAlias(),
                        ];
                        $arr[ $v->getAlias() ]['pages'][ $avtor->getAlias()  ] = $ar;
                        foreach ($avtor->getBooks() as $v1) {
                            $ar = [];
                            $ar['route'] = 'home/authors/one/book';
                            $ar['label'] = $v1->getName();
                            $ar['class'] = 'entypo-book';
                            $ar['label_eng'] = $v1->getAlias();
                            $ar['vis'] = 1;
                            $ar['params'] = [
                                'alias_menu' => $avtor->getAlias(),
                                'book'       => $v1->getAlias(),
                            ];
                            $arr[ $v->getAlias() ]['pages'][ $avtor->getAlias() ]['pages'][ $ar['label_eng'] ] = $ar;
                        }
                        if ($pageBookCount != 0) {
                            $arr2 = [];
                            for ($i = 1; $i <= $pageBookCount; $i++) {
                                $ar1 = [];
                                $ar1['route'] = 'home/authors/one/book/read';
                                $ar1['label'] = "Страница " . $i;
                                $ar1['label_eng'] = "Page_" . $i;
                                $ar1['vis'] = 1;
                                $ar1['class'] = 'entypo-feather';

                                $ar1['params'] = [
                                    'book'       => $book->getAlias(),
                                    'alias_menu' => $avtor->getAlias(),
                                    'page_str'   => $i
                                ];
                                $arr1 = [];
                                $arr1['route'] = 'home/authors/one/book/read';
                                $arr1['label'] = "Страницы";
                                $arr1['label_eng'] = "Pages_str";
                                $arr1['vis'] = 1;

                                $arr1['params'] = [
                                    'book'       => $book->getAlias(),
                                    'alias_menu' => $avtor->getAlias(),
                                    'page_str'   => ""
                                ];
                                $arr2[ $ar1['label_eng'] ] = $ar1;
                            }
                            $arr[ $v->getAlias() ]['pages'][  $avtor->getAlias() ]['pages'][  $book->getAlias()  ]['pages'][ $arr1['label_eng'] ] = $arr1;
                            $arr[ $v->getAlias() ]['pages'][  $avtor->getAlias() ]['pages'][  $book->getAlias()  ]['pages'][ $arr1['label_eng'] ]['pages'] = $arr2;
                        }
                        if (count($soder) != 0) {
                            $arr2 = [];
                            foreach ($soder as $v1) {
                                $ar1 = [];
                                $ar1['route'] = 'home/authors/one/book/content';
                                $ar1['label'] = $v1->getName();
                                $ar1['label_eng'] = $v1->getAlias();
                                $ar1['vis'] = 1;
                                $ar1['class'] = 'entypo-feather';
                                $ar1['params'] = [
                                    'book'       => $book->getAlias(),
                                    'alias_menu' => $avtor->getAlias(),
                                    'content'    => $v1->getAlias()
                                ];

                                $arr1 = [];
                                $arr1['route'] = 'home/authors/one/book/content';
                                $arr1['label'] = "Содержание";
                                $arr1['label_eng'] = "Contents_str";
                                $arr1['vis'] = 1;

                                $arr1['params'] = [
                                    'book'       => $book->getAlias(),
                                    'alias_menu' => $avtor->getAlias(),
                                    'content'    => ""
                                ];
                                $arr2[ $ar1['label_eng'] ] = $ar1;
                            }
                            $arr[ $v->getAlias() ]['pages'][ $avtor->getAlias() ]['pages'][ $book->getAlias() ]['pages'][ $arr1['label_eng'] ] = $arr1;
                            $arr[ $v->getAlias() ]['pages'][ $avtor->getAlias() ]['pages'][ $book->getAlias() ]['pages'][ $arr1['label_eng'] ]['pages'] = $arr2;
                        }
                    }
                }
                if ($book and $book->getMenuId() == $v->getId()) {
                    $ar = [];
                    $ar['route'] = $book->getRoute();
                    $ar['label'] = $book->getName();
                    $ar['class'] = 'entypo-book';
                    $ar['label_eng'] = $book->getAlias();
                    $ar['vis'] = $book->getVis();
                    $ar['params'] = [
                        'book'       => $book->getAlias(),
                        's'          => $book->getNS(),
                        'alias_menu' => $book->getNAliasMenu(),
                    ];
                    $arr[ $v->getAlias() ]['pages'][ $book->getAlias() ] = $ar;
                    if ($pageBookCount != 0) {
                        $arr2 = [];
                        for ($i = 1; $i <= $pageBookCount; $i++) {
                            $ar = [];
                            $ar['route'] = 'home/genre/one/book/read';
                            $ar['label'] = "Страница " . $i;
                            $ar['label_eng'] = "Page_" . $i;
                            $ar['vis'] = 1;
                            $ar['class'] = 'entypo-feather';

                            $ar['params'] = [
                                'book'       => $book->getAlias(),
                                's'          => $book->getNS(),
                                'alias_menu' => $book->getNAliasMenu(),
                                'page_str'   => $i
                            ];

                            $arr1 = [];
                            $arr1['route'] = 'home/genre/one/book/read';
                            $arr1['label'] = "Страницы";
                            $arr1['label_eng'] = "Pages_str";
                            $arr1['vis'] = 1;

                            $arr1['params'] = [
                                'book'       => $book->getAlias(),
                                's'          => $book->getNS(),
                                'alias_menu' => $book->getNAliasMenu(),
                                'page_str'   => ""
                            ];
                            $arr2[ $ar['label_eng'] ] = $ar;
                        }

                        $arr[ $v->getAlias()]['pages'][ $book->getAlias() ]['pages'][ $arr1['label_eng'] ] = $arr1;
                        $arr[ $v->getAlias() ]['pages'][ $book->getAlias() ]['pages'][ $arr1['label_eng'] ]['pages'] = $arr2;
                    }

                    if (count($soder) != 0) {
                        $arr2 = [];
                        foreach ($soder as $v1) {
                            $ar = [];
                            $ar['route'] = 'home/genre/one/book/content';
                            $ar['label'] = $v1->getName();
                            $ar['label_eng'] = $v1->getAlias();
                            $ar['vis'] = 1;
                            $ar['class'] = 'entypo-feather';
                            $ar['params'] = [
                                'book'       =>  $book->getAlias(),
                                's'          => $book->getNS(),
                                'alias_menu' => $book->getNAliasMenu(),
                                'content'    => $v1->getAlias()
                            ];

                            $arr1 = [];
                            $arr1['route'] = 'home/genre/one/book/content';
                            $arr1['label'] = "Содержание";
                            $arr1['label_eng'] = "Contents_str";
                            $arr1['vis'] = 1;

                            $arr1['params'] = [
                                'book'       =>  $book->getAlias(),
                                's'          => $book->getNS(),
                                'alias_menu' => $book->getNAliasMenu(),
                                'content'    => ""
                            ];
                            $arr2[ $ar['label_eng'] ] = $ar;
                        }
                        $arr[ $v->getAlias() ]['pages'][ $book->getAlias() ]['pages'][ $arr1['label_eng'] ] = $arr1;
                        $arr[ $v->getAlias() ]['pages'][  $book->getAlias() ]['pages'][ $arr1['label_eng'] ]['pages'] = $arr2;
                    }
                }

            }
        }

        return $arr;
    }

}
