<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Navigation\Service\DefaultNavigationFactory;
use Zend\Db\Sql\Expression;

class NavigationDynamic extends DefaultNavigationFactory {
    protected $sm = false;

    protected function getPages(ServiceLocatorInterface $serviceLocator) {

        $this->sm = $serviceLocator;

        if (null === $this->pages) {
            $application = $serviceLocator->get('Application');
            $routeMatch = $application->getMvcEvent()->getRouteMatch();
            $router = $application->getMvcEvent()->getRouter();
            $where = "see = 1";
            $order = "id_main ASC, id ASC";
            $fetchMenuObject = $serviceLocator->get('Application\Model\MZhanrTable')->fetchAll(false, $order, $where);
            $book = false;
            //var_dump($routeMatch->getMatchedRouteName());
            $pageBookCount = 0;
            $soder = 0;
            $avtor = 0;
            $serii = 0;
            $translit = 0;
            if (!empty($routeMatch)) {
                if ($routeMatch->getMatchedRouteName() == 'home/genre/one/book' or $routeMatch->getMatchedRouteName() == 'home/genre/one/book/read' or $routeMatch->getMatchedRouteName() == 'home/genre/one/book/content') {
                    $bookAlias = $routeMatch->getParam('book');
                    $where = "alias = '{$bookAlias}' and vis = 1";
                    $BookObject = $serviceLocator->get('Application\Model\BookTable')
                        ->joinZhanr()
                        ->joinColumn(
                            [
                                new Expression('zhanr.id_menu as id_menu'),
                                'id',
                                'alias',
                                'name',
                                'route',
                                'vis'
                            ]
                        )
                        ->fetchAll(false, false, $where);
                    if (count($BookObject) != 0) {
                        $BookObject = $BookObject[0];
                        $book = (array)$BookObject;
                        $where = "id_main = '{$book['id']}'";
                        $pageBook = $serviceLocator->get('Application\Model\TextTable')->fetchAll(false, false, $where);
                        $pageBookCount = $pageBook->count();


                        $where = "id_main = '{$book['id']}'";
                        $soder = $serviceLocator->get('Application\Model\SoderTable')->fetchAll(false, false, $where);
                    }
                }
                elseif ($routeMatch->getMatchedRouteName() == 'home/authors/one' or $routeMatch->getMatchedRouteName() == 'home/authors/one/book' or $routeMatch->getMatchedRouteName() == 'home/authors/one/book/read' or $routeMatch->getMatchedRouteName() == 'home/authors/one/book/content') {
                    $alias_author = $routeMatch->getParam('alias_menu');
                    $where = "m_avtor.alias = '{$alias_author}'";
                    $avtor = $serviceLocator->get('Application\Model\MAvtorTable')->joinAvtor()->joinBook()->fetchAll(false, false, $where);

                    if ($routeMatch->getMatchedRouteName() == 'home/authors/one/book' or $routeMatch->getMatchedRouteName() == 'home/authors/one/book/read' or $routeMatch->getMatchedRouteName() == 'home/authors/one/book/content') {
                        $bookAlias = $routeMatch->getParam('book');
                        $where = "alias = '{$bookAlias}' and vis = 1";
                        $BookObject = $serviceLocator->get('Application\Model\BookTable')
                            ->joinZhanr()
                            ->joinColumn(
                                [
                                    new Expression('zhanr.id_menu as id_menu'),
                                    'id',
                                    'alias',
                                    'name',
                                    'route',
                                    'vis'
                                ]
                            )
                            ->fetchAll(false, false, $where);
                        if (count($BookObject) != 0) {
                            $BookObject = $BookObject[0];
                            $book = (array)$BookObject;
                            $where = "id_main = '{$book['id']}'";
                            $pageBook = $serviceLocator->get('Application\Model\TextTable')->fetchAll(false, false, $where);
                            $pageBookCount = $pageBook->count();


                            $where = "id_main = '{$book['id']}'";
                            $soder = $serviceLocator->get('Application\Model\SoderTable')->fetchAll(false, false, $where);
                        }
                    }
                }

                elseif ($routeMatch->getMatchedRouteName() == 'home/series/one' or $routeMatch->getMatchedRouteName() == 'home/series/one/book' or $routeMatch->getMatchedRouteName() == 'home/series/one/book/read' or $routeMatch->getMatchedRouteName() == 'home/series/one/book/content') {
                    $alias_series = $routeMatch->getParam('alias_menu');
                    $where = "m_serii.alias = '{$alias_series}'";
                    $serii = $serviceLocator->get('Application\Model\MSeriiTable')->joinSerii()->joinBook()->fetchAll(false, false, $where);

                    if ($routeMatch->getMatchedRouteName() == 'home/series/one/book' or $routeMatch->getMatchedRouteName() == 'home/series/one/book/read' or $routeMatch->getMatchedRouteName() == 'home/series/one/book/content') {
                        $bookAlias = $routeMatch->getParam('book');
                        $where = "alias = '{$bookAlias}' and vis = 1";
                        $BookObject = $serviceLocator->get('Application\Model\BookTable')
                            ->joinZhanr()
                            ->joinColumn(
                                [
                                    new Expression('zhanr.id_menu as id_menu'),
                                    'id',
                                    'alias',
                                    'name',
                                    'route',
                                    'vis'
                                ]
                            )
                            ->fetchAll(false, false, $where);
                        if (count($BookObject) != 0) {
                            $BookObject = $BookObject[0];
                            $book = (array)$BookObject;
                            $where = "id_main = '{$book['id']}'";
                            $pageBook = $serviceLocator->get('Application\Model\TextTable')->fetchAll(false, false, $where);
                            $pageBookCount = $pageBook->count();
                            $where = "id_main = '{$book['id']}'";
                            $soder = $serviceLocator->get('Application\Model\SoderTable')->fetchAll(false, false, $where);
                        }
                    }
                }
                elseif ($routeMatch->getMatchedRouteName() == 'home/translit/one' or $routeMatch->getMatchedRouteName() == 'home/translit/one/book' or $routeMatch->getMatchedRouteName() == 'home/translit/one/book/read' or $routeMatch->getMatchedRouteName() == 'home/translit/one/book/content') {
                    $alias_series = $routeMatch->getParam('alias_menu');
                    $where = "m_translit.alias = '{$alias_series}'";
                    $translit = $serviceLocator->get('Application\Model\MTranslitTable')->joinTranslit()->joinBook()->fetchAll(false, false, $where);

                    if ($routeMatch->getMatchedRouteName() == 'home/translit/one/book' or $routeMatch->getMatchedRouteName() == 'home/translit/one/book/read' or $routeMatch->getMatchedRouteName() == 'home/translit/one/book/content') {
                        $bookAlias = $routeMatch->getParam('book');
                        $where = "book.alias = '{$bookAlias}' and vis = 1";

                        $BookObject = $serviceLocator->get('Application\Model\BookTable')
                            ->joinZhanr()
                            ->joinColumn(
                                [
                                    new Expression('zhanr.id_menu as id_menu'),
                                    'id',
                                    'alias',
                                    'name',
                                    'route',
                                    'vis'
                                ]
                            )
                            ->fetchAll(false, false, $where);
                        if (count($BookObject) != 0) {
                            $BookObject = $BookObject[0];
                            $book = (array)$BookObject;
                            $where = "id_main = '{$book['id']}'";
                            $pageBook = $serviceLocator->get('Application\Model\TextTable')->fetchAll(false, false, $where);
                            $pageBookCount = $pageBook->count();


                            $where = "id_main = '{$book['id']}'";
                            $soder = $serviceLocator->get('Application\Model\SoderTable')->fetchAll(false, false, $where);
                        }
                    }


                }

            }
            $min = $fetchMenuObject->current();

            $fetchMenuArray = [];
            foreach ($fetchMenuObject as $v) {
               $fetchMenuArray[] = $v->arr;
            }

            $menu = $this->genMenu($fetchMenuArray, $min->arr['id_main'], 0, $book, $pageBookCount, $soder, $avtor, $serii, $translit);

            $pages = $this->getPagesFromConfig($menu);

            $this->pages = $this->injectComponents($pages, $routeMatch, $router);
        }

        return $this->pages;
    }

    protected function genMenu($fetchMenuArray, $menu = 0, $parent = 0, $book, $pageBookCount = 0, $soder = 0, $avtor = 0, $serii = 0, $translit = 0) {
        global $site;
        $arr = false;
        $t = true;
        foreach ($fetchMenuArray as $v) {
            $v['route'] = str_replace('/slash','',  $v['route']);
            if ($v['id_main'] == $menu) {
                $ar = [];
                $ar['route'] = $v['route'];
                if($v['count_book'] == 0){
                    $ar['label'] = $v['name'];
                }
                else{
                    $ar['label'] = $v['name'].' '.$v['count_book'];
                }
                $ar['class'] = $v['icon'];
                $ar['label_eng'] = $v['alias'];
                $ar['vis'] = $v['vis'];
                $arr[ $v['alias'] ] = $ar;
                if ($v['vis'] and !empty($menu['s'])) {
                    $ar['params']['s'] = $menu['s'];
                    $ar['params']['alias_menu'] = $v['alias'];
                    $ar['s'] = $menu['s'] . '/' . $v['alias'];
                }
                elseif ($v['vis'] and empty($menu['s'])) {
                    $ar['s'] = $v['alias'];
                    $ar['params']['alias_menu'] = $v['alias'];
                }
                if ($v['action']) {
                    //$ar['params']['alias_m'] = $v['action'];
                    $ar['action'] = $v['action'];
                    //$ar['controller'] = 'Application\Controller\Index';
                }

                $t = $this->genMenu($fetchMenuArray, $ar, $v['id'], $book, $pageBookCount, $soder, $avtor, $serii, $translit);
                if (!empty($t)) $arr[ $v['alias'] ]['pages'] = $t;


            }
            elseif ($v['id_main'] == $parent) {
                $ar = [];
                $ar['route'] = $v['route'];
                //$ar['action'] =  'genre';
                if($v['count_book'] == 0){
                    $ar['label'] = $v['name'];
                }
                else{
                    $ar['label'] = $v['name'].' '.$v['count_book'];
                }
                $ar['class'] = $v['icon'];
                $ar['label_eng'] = $v['alias'];
                $ar['vis'] = $v['vis'];
                if ($v['vis'] and !empty($menu['s'])) {
                    $ar['params']['s'] = $menu['s'];
                    $ar['params']['alias_menu'] = $v['alias'];
                    $ar['s'] = $menu['s'] . '/' . $v['alias'];
                }
                elseif ($v['vis'] and empty($menu['s'])) {
                    $ar['s'] = $v['alias'];
                    $ar['params']['alias_menu'] = $v['alias'];;
                }

                if ($v['action']) {
                    //$ar['params']['alias_m'] = $v['action'];
                    $ar['action'] = $v['action'];
                    //$ar['controller'] = 'Application\Controller\Index';
                }
                $arr[ $v['alias'] ] = $ar;
                $t = $this->genMenu($fetchMenuArray, $ar, $v['id'], $book, $pageBookCount, $soder, $avtor, $serii, $translit);
                if (!empty($t)) $arr[ $v['alias'] ]['pages'] = $t;

                if ($serii and $serii->count()) {

                    if ($v['route'] == 'home/series') {
                        $arr2 = [];
                        $serii->rewind();
                        $serii_current = $serii->current();
                        // var_dump($avtor_current);die();
                        $ar = [];
                        $ar['route'] = 'home/series/one';
                        $ar['label'] = $serii_current->name;
                        $ar['class'] = 'entypo-user';
                        $ar['label_eng'] = $serii_current->alias;
                        $ar['vis'] = 1;
                        $ar['params'] = [
                            'alias_menu' => $serii_current->alias,
                        ];
                        $arr[ $v['alias'] ]['pages'][ $book['alias'] ] = $ar;
                        foreach ($serii as $v1) {
                            $ar = [];
                            $ar['route'] = 'home/series/one/book';
                            $ar['label'] = $v1->book_name;
                            $ar['class'] = 'entypo-book';
                            $ar['label_eng'] = $v1->book_alias;
                            $ar['vis'] = 1;
                            $ar['params'] = [
                                'alias_menu' => $serii_current->alias,
                                'book'       => $v1->book_alias,
                            ];
                            $arr[ $v['alias'] ]['pages'][ $book['alias'] ]['pages'][ $ar['label_eng'] ] = $ar;
                        }
                        if (!empty($pageBookCount)) {
                            $arr2 = [];
                            for ($i = 1; $i <= $pageBookCount; $i++) {
                                $ar1 = [];
                                $ar1['route'] = 'home/series/one/book/read';
                                $ar1['label'] = "Страница " . $i;
                                $ar1['label_eng'] = "Page_" . $i;
                                $ar1['vis'] = 1;
                                $ar1['class'] = 'entypo-feather';

                                $ar1['params'] = [
                                    'book'       => $book['alias'],
                                    'alias_menu' => $serii_current->alias,
                                    'page_str'   => $i
                                ];
                                $arr1 = [];
                                $arr1['route'] = 'home/series/one/book/read';
                                $arr1['label'] = "Страницы";
                                $arr1['label_eng'] = "Pages_str";
                                $arr1['vis'] = 1;

                                $arr1['params'] = [
                                    'book'       => $book['alias'],
                                    'alias_menu' => $serii_current->alias,
                                    'page_str'   => ""
                                ];
                                $arr2[ $ar1['label_eng'] ] = $ar1;
                            }
                            $arr[ $v['alias'] ]['pages'][ $book['alias'] ]['pages'][ $book['alias'] ]['pages'][ $arr1['label_eng'] ] = $arr1;
                            $arr[ $v['alias'] ]['pages'][ $book['alias'] ]['pages'][ $book['alias'] ]['pages'][ $arr1['label_eng'] ]['pages'] = $arr2;
                        }
                        if($soder and $soder->count() != 0){

                            $arr2 = [];
                            $soder->rewind();
                            $arr3 = $soder->current();

                            foreach ($soder as $v1) {
                                    $v1 = $v1->arr;
                                    $ar1 = [];
                                    $ar1['route'] = 'home/series/one/book/content';
                                    $ar1['label'] = $v1['name'];
                                    $ar1['label_eng'] = $v1['alias'];
                                    $ar1['vis'] = 1;
                                    $ar1['class'] = 'entypo-feather';
                                    $ar1['params'] = [
                                        'book'       => $book['alias'],
                                        'alias_menu' => $serii_current->alias,
                                        'content'    => $v1['alias']
                                    ];

                                    $arr1 = [];
                                    $arr1['route'] = 'home/series/one/book/content';
                                    $arr1['label'] = "Содержание";
                                    $arr1['label_eng'] = "Contents_str";
                                    $arr1['vis'] = 1;

                                    $arr1['params'] = [
                                        'book'       => $book['alias'],
                                        'alias_menu' => $serii_current->alias,
                                        'content'    => ""
                                    ];
                                    $arr2[ $ar1['label_eng'] ] = $ar1;
                                }

                            $arr[ $v['alias'] ]['pages'][ $book['alias'] ]['pages'][ $book['alias'] ]['pages'][ $arr1['label_eng'] ] = $arr1;
                            $arr[ $v['alias'] ]['pages'][ $book['alias'] ]['pages'][ $book['alias'] ]['pages'][ $arr1['label_eng'] ]['pages'] = $arr2;
                        }
                    }
                }
                if ($translit and $translit->count()) {

                    if ($v['route'] == 'home/translit') {
                        $arr2 = [];
                        $translit->rewind();
                        $translit_current = $translit->current();
                        // var_dump($avtor_current);die();
                        $ar = [];
                        $ar['route'] = 'home/translit/one';
                        $ar['label'] = $translit_current->name;
                        $ar['class'] = 'entypo-user';
                        $ar['label_eng'] = $translit_current->alias;
                        $ar['vis'] = 1;
                        $ar['params'] = [
                            'alias_menu' => $translit_current->alias,
                        ];
                        $arr[ $v['alias'] ]['pages'][ $book['alias'] ] = $ar;
                        foreach ($translit as $v1) {
                            $ar = [];
                            $ar['route'] = 'home/translit/one/book';
                            $ar['label'] = $v1->book_name;
                            $ar['class'] = 'entypo-book';
                            $ar['label_eng'] = $v1->book_alias;
                            $ar['vis'] = 1;
                            $ar['params'] = [
                                'alias_menu' => $translit_current->alias,
                                'book'       => $v1->book_alias,
                            ];
                            $arr[ $v['alias'] ]['pages'][ $book['alias'] ]['pages'][ $ar['label_eng'] ] = $ar;
                        }
                        if (!empty($pageBookCount)) {
                            $arr2 = [];
                            for ($i = 1; $i <= $pageBookCount; $i++) {
                                $ar1 = [];
                                $ar1['route'] = 'home/translit/one/book/read';
                                $ar1['label'] = "Страница " . $i;
                                $ar1['label_eng'] = "Page_" . $i;
                                $ar1['vis'] = 1;
                                $ar1['class'] = 'entypo-feather';

                                $ar1['params'] = [
                                    'book'       => $book['alias'],
                                    'alias_menu' => $translit_current->alias,
                                    'page_str'   => $i
                                ];
                                $arr1 = [];
                                $arr1['route'] = 'home/translit/one/book/read';
                                $arr1['label'] = "Страницы";
                                $arr1['label_eng'] = "Pages_str";
                                $arr1['vis'] = 1;

                                $arr1['params'] = [
                                    'book'       => $book['alias'],
                                    'alias_menu' => $translit_current->alias,
                                    'page_str'   => ""
                                ];
                                $arr2[ $ar1['label_eng'] ] = $ar1;
                            }
                            $arr[ $v['alias'] ]['pages'][ $book['alias'] ]['pages'][ $book['alias'] ]['pages'][ $arr1['label_eng'] ] = $arr1;
                            $arr[ $v['alias'] ]['pages'][ $book['alias'] ]['pages'][ $book['alias'] ]['pages'][ $arr1['label_eng'] ]['pages'] = $arr2;
                        }
                        if($soder and $soder->count() != 0){
                            $arr2 = [];
                            $soder->rewind();
                            $arr3 = $soder->current();
                            foreach ($soder as $v1) {

                                    $v1 = $v1->arr;
                                    $ar1 = [];
                                    $ar1['route'] = 'home/translit/one/book/content';
                                    $ar1['label'] = $v1['name'];
                                    $ar1['label_eng'] = $v1['alias'];
                                    $ar1['vis'] = 1;
                                    $ar1['class'] = 'entypo-feather';
                                    $ar1['params'] = [
                                        'book'       => $book['alias'],
                                        'alias_menu' => $translit_current->alias,
                                        'content'    => $v1['alias']
                                    ];

                                    $arr1 = [];
                                    $arr1['route'] = 'home/translit/one/book/content';
                                    $arr1['label'] = "Содержание";
                                    $arr1['label_eng'] = "Contents_str";
                                    $arr1['vis'] = 1;

                                    $arr1['params'] = [
                                        'book'       => $book['alias'],
                                        'alias_menu' => $translit_current->alias,
                                        'content'    => ""
                                    ];
                                    $arr2[ $ar1['label_eng'] ] = $ar1;
                            }
                            $arr[ $v['alias'] ]['pages'][ $book['alias'] ]['pages'][ $book['alias'] ]['pages'][ $arr1['label_eng'] ] = $arr1;
                            $arr[ $v['alias'] ]['pages'][ $book['alias'] ]['pages'][ $book['alias'] ]['pages'][ $arr1['label_eng'] ]['pages'] = $arr2;
                        }
                    }
                }


                if ($avtor and $avtor->count()) {

                    if ($v['route'] == 'home/authors') {
                        $arr2 = [];
                        $avtor->rewind();
                        $avtor_current = $avtor->current();
                        // var_dump($avtor_current);die();
                        $ar = [];
                        $ar['route'] = 'home/authors/one';
                        $ar['label'] = $avtor_current->name;
                        $ar['class'] = 'entypo-user';
                        $ar['label_eng'] = $avtor_current->alias;
                        $ar['vis'] = 1;
                        $ar['params'] = [
                            'alias_menu' => $avtor_current->alias,
                        ];
                        $arr[ $v['alias'] ]['pages'][ $book['alias'] ] = $ar;
                        foreach ($avtor as $v1) {
                            $ar = [];
                            $ar['route'] = 'home/authors/one/book';
                            $ar['label'] = $v1->book_name;
                            $ar['class'] = 'entypo-book';
                            $ar['label_eng'] = $v1->book_alias;
                            $ar['vis'] = 1;
                            $ar['params'] = [
                                'alias_menu' => $avtor_current->alias,
                                'book'       => $v1->book_alias,
                            ];
                            $arr[ $v['alias'] ]['pages'][ $book['alias'] ]['pages'][ $ar['label_eng'] ] = $ar;
                        }

                        if (!empty($pageBookCount)) {
                            $arr2 = [];
                            for ($i = 1; $i <= $pageBookCount; $i++) {
                                $ar1 = [];
                                $ar1['route'] = 'home/authors/one/book/read';
                                $ar1['label'] = "Страница " . $i;
                                $ar1['label_eng'] = "Page_" . $i;
                                $ar1['vis'] = 1;
                                $ar1['class'] = 'entypo-feather';

                                $ar1['params'] = [
                                    'book'       => $book['alias'],
                                    'alias_menu' => $avtor_current->alias,
                                    'page_str'   => $i
                                ];
                                $arr1 = [];
                                $arr1['route'] = 'home/authors/one/book/read';
                                $arr1['label'] = "Страницы";
                                $arr1['label_eng'] = "Pages_str";
                                $arr1['vis'] = 1;

                                $arr1['params'] = [
                                    'book'       => $book['alias'],
                                    'alias_menu' => $avtor_current->alias,
                                    'page_str'   => ""
                                ];
                                $arr2[ $ar1['label_eng'] ] = $ar1;
                            }
                            $arr[ $v['alias'] ]['pages'][ $book['alias'] ]['pages'][ $book['alias'] ]['pages'][ $arr1['label_eng'] ] = $arr1;
                            $arr[ $v['alias'] ]['pages'][ $book['alias'] ]['pages'][ $book['alias'] ]['pages'][ $arr1['label_eng'] ]['pages'] = $arr2;
                        }
                        if($soder and $soder->count() != 0){
                            $arr2 = [];
                            $soder->rewind();
                            $arr3 = $soder->current();
                            foreach ($soder as $v1) {
                                    $v1 = $v1->arr;
                                    $ar1 = [];
                                    $ar1['route'] = 'home/authors/one/book/content';
                                    $ar1['label'] = $v1['name'];
                                    $ar1['label_eng'] = $v1['alias'];
                                    $ar1['vis'] = 1;
                                    $ar1['class'] = 'entypo-feather';
                                    $ar1['params'] = [
                                        'book'       => $book['alias'],
                                        'alias_menu' => $avtor_current->alias,
                                        'content'    => $v1['alias']
                                    ];

                                    $arr1 = [];
                                    $arr1['route'] = 'home/authors/one/book/content';
                                    $arr1['label'] = "Содержание";
                                    $arr1['label_eng'] = "Contents_str";
                                    $arr1['vis'] = 1;

                                    $arr1['params'] = [
                                        'book'       => $book['alias'],
                                        'alias_menu' => $avtor_current->alias,
                                        'content'    => ""
                                    ];
                                    $arr2[ $ar1['label_eng'] ] = $ar1;
                            }
                            $arr[ $v['alias'] ]['pages'][ $book['alias'] ]['pages'][ $book['alias'] ]['pages'][ $arr1['label_eng'] ] = $arr1;
                            $arr[ $v['alias'] ]['pages'][ $book['alias'] ]['pages'][ $book['alias'] ]['pages'][ $arr1['label_eng'] ]['pages'] = $arr2;
                        }
                    }
                }

                if ($book['id_menu'] == $v['id']) {
                    $ar = [];
                    $ar['route'] = $book['route'];
                    $ar['label'] = $book['name'];
                    $ar['class'] = 'entypo-book';
                    $ar['label_eng'] = $book['alias'];
                    $ar['vis'] = $book['vis'];
                    $ar['params'] = [
                        'book'       => $book['alias'],
                        's'          => $menu['s'],
                        'alias_menu' => $v['alias'],
                    ];
                    $arr[ $v['alias'] ]['pages'][ $book['alias'] ] = $ar;

                    if (!empty($pageBookCount)) {
                        $arr2 = [];
                        for ($i = 1; $i <= $pageBookCount; $i++) {
                            $ar = [];
                            $ar['route'] = 'home/genre/one/book/read';
                            $ar['label'] = "Страница " . $i;
                            $ar['label_eng'] = "Page_" . $i;
                            $ar['vis'] = 1;
                            $ar['class'] = 'entypo-feather';

                            $ar['params'] = [
                                'book'       => $book['alias'],
                                's'          => $menu['s'],
                                'alias_menu' => $v['alias'],
                                'page_str'   => $i
                            ];

                            $arr1 = [];
                            $arr1['route'] = 'home/genre/one/book/read';
                            $arr1['label'] = "Страницы";
                            $arr1['label_eng'] = "Pages_str";
                            $arr1['vis'] = 1;

                            $arr1['params'] = [
                                'book'       => $book['alias'],
                                's'          => $menu['s'],
                                'alias_menu' => $v['alias'],
                                'page_str'   => ""
                            ];
                            $arr2[ $ar['label_eng'] ] = $ar;
                        }

                        $arr[ $v['alias'] ]['pages'][ $book['alias'] ]['pages'][ $arr1['label_eng'] ] = $arr1;
                        $arr[ $v['alias'] ]['pages'][ $book['alias'] ]['pages'][ $arr1['label_eng'] ]['pages'] = $arr2;
                    }
                    if (!empty($soder) and $soder->count() != 0) {
                        $arr2 = [];
                        $soder->rewind();
                        $arr3 = $soder->current();
                        foreach ($soder as $v1) {
                                $v1 = $v1->arr;
                                $ar = [];
                                $ar['route'] = 'home/genre/one/book/content';
                                $ar['label'] = $v1['name'];
                                $ar['label_eng'] = $v1['alias'];
                                $ar['vis'] = 1;
                                $ar['class'] = 'entypo-feather';
                                $ar['params'] = [
                                    'book'       => $book['alias'],
                                    's'          => $menu['s'],
                                    'alias_menu' => $v['alias'],
                                    'content'    => $v1['alias']
                                ];

                                $arr1 = [];
                                $arr1['route'] = 'home/genre/one/book/content';
                                $arr1['label'] = "Содержание";
                                $arr1['label_eng'] = "Contents_str";
                                $arr1['vis'] = 1;

                                $arr1['params'] = [
                                    'book'       => $book['alias'],
                                    's'          => $menu['s'],
                                    'alias_menu' => $v['alias'],
                                    'content'    => ""
                                ];
                                $arr2[ $ar['label_eng'] ] = $ar;
                        }
                        $arr[ $v['alias'] ]['pages'][ $book['alias'] ]['pages'][ $arr1['label_eng'] ] = $arr1;
                        $arr[ $v['alias'] ]['pages'][ $book['alias'] ]['pages'][ $arr1['label_eng'] ]['pages'] = $arr2;
                    }
                }

            }
        }

        return $arr;
    }

    protected function checkSlash($id_menu, $fetchMenuArray) {
        foreach ($fetchMenuArray as $v) {
            if ($v['id_main'] == $id_menu) return true;
        }

        return false;
    }

}
