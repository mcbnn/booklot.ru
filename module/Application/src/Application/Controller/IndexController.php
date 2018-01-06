<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Application\Entity\Book;
use Application\Entity\MZhanr;
use Application\Entity\MAvtor;
use Application\Entity\MSerii;
use Application\Entity\MTranslit;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator as ZendPaginator;

class IndexController extends AbstractActionController
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
        $page = $this->params()->fromRoute('paged', 1);
        if ($page == 1) {
            $this->noindex(false);
        } else {
            $this->noindex(true);
        }
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Repository\BookRepository */
        $repository = $em->getRepository(Book::class);
        $query = $repository->getBooksQuery($this->getServiceLocator()->get('arraySort'));
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(27);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(6);
        $vm = new ViewModel(
            [
                'paginator' => $paginator
            ]
        );
        return $vm;
    }

    /**
     * @return void|ViewModel
     */
    public function oneGenreAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        $alias_menu = $this->params()->fromRoute('alias_menu');
        $ns = $this->params()->fromRoute('s', null);
        if($this->params()->fromRoute('paged')){
            $this->getResponse()->setStatusCode(404);
            return;
        }
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Repository\MZhanrRepository */
        $mzhanr = $em->getRepository(MZhanr::class)->findOneBy(['alias' => $alias_menu]);;
        if(!$mzhanr){
            $this->getResponse()->setStatusCode(404);
            return;
        }
        if ($page == 1) {
            $this->noindex(false);
        } else {
            $this->noindex(true);
        }
        /** @var  $repository \Application\Repository\BookRepository */
        $repository = $em->getRepository(Book::class);
        if(!$ns){
            $where = ['where' => [
                'b_nS' => [
                    'column' => 'b.nS',
                    'type' => '=',
                    'value' => $alias_menu,
                    'operator' => 'and'
                ]
            ]
            ];
        }
        else{
            $where = ['where' => [
                'b_nAliasMenu' => [
                    'column' => 'b.nAliasMenu',
                    'type' => '=',
                    'value' => $alias_menu,
                    'operator' => 'and'
                ],
            ]
            ];
        }
        $query = $repository->getBooksQuery(
            $this->getServiceLocator()->get('arraySort'),
            $where
        );
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(27);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(6);

        $this->seo(
            $mzhanr->getName().' читать онлайн',
            $mzhanr->getName().' читать онлайн',
            $mzhanr->getDescription(),
            $mzhanr->getKeywords()
        );
        $vm = new ViewModel(
            [
                'paginator' => $paginator,
                'zhanr'     => $mzhanr
            ]
        );
        return $vm;
    }


    /**
     * @return JsonModel
     */
    public function ajaxsearchAction()
    {
        $dataBase = $this->getServiceLocator()->get('AjaxSearch');
        return new JsonModel($dataBase);
    }

    /**
     * @return ViewModel
     */
    public function searchAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        if ($page == 1) {
            $this->noindex(false);
        } else {
            $this->noindex(true);
        }
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Repository\BookRepository */
        $repository = $em->getRepository(Book::class);
        $query = $repository->getBooksQuery(
            $this->getServiceLocator()->get('arraySort'),
            $this->getServiceLocator()->get('arrayWhere')
        );
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(27);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(6);
        $vm = new ViewModel(
            [
                'paginator' => $paginator
            ]
        );
        $vm->setTemplate('application/index/search-tempalte');
        return $vm;
    }

    /**
     * @return ViewModel
     */
    public function authorsAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Repository\MAvtorRepository */
        $repository = $em->getRepository(MAvtor::class);
        $query = $repository->getAvtors();
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(200);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(6);
        $menu = $em->getRepository(MZhanr::class)->findOneBy(['route' => 'home/authors']);
        $this->seo(
            'Авторы',
            'Авторы',
            $menu->getDescription(),
            $menu->getKeywords()
        );
        $vm = new ViewModel(
            [
                'paginator' => $paginator,
                'menu' => $menu
            ]
        );
        return $vm;
    }

    /**
     * @return void|ViewModel
     */
    public function authorAction()
    {
        $page = $this->params()->fromRoute('page_author', 1);
        $alias_menu = $this->params()->fromRoute('alias_menu');
        if($this->params()->fromRoute('paged')){
            $this->getResponse()->setStatusCode(404);
            return;
        }
        if ($page == 1) {
            $this->noindex(false);
        } else {
            $this->noindex(true);
        }
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Repository\MAvtorRepository */
        $avtor = $em->getRepository(MAvtor::class)->findOneBy(['alias' => $alias_menu]);
        if(!$avtor){
            $this->getResponse()->setStatusCode(404);
            return;
        }
        /** @var  $repository \Application\Repository\BookRepository */
        $repository = $em->getRepository(Book::class);

        $where = [
            'where' => [
                'ma_name' => [
                    'column'   => 'ma.alias',
                    'type'     => '=',
                    'value'    => $alias_menu,
                    'operator' => 'and',
                ],
            ],
        ];
        $query = $repository->getBooksQuery(
            $this->getServiceLocator()->get('arraySort'),
            $where
        );
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(27);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(6);
        $title = "Автор - ".$avtor->getName();
        $this->seo($avtor->getName(), $avtor->getName());

        $vm = new ViewModel(
            [
                'paginator' => $paginator,
                'title' => $title
            ]
        );
        return $vm;
    }

    /**
     * @return void|ViewModel
     */
    public function seriesAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Repository\MSeriiRepository */
        $repository = $em->getRepository(MSerii::class);
        $query = $repository->getSerii();
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(200);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(6);
        $menu = $em->getRepository(MZhanr::class)->findOneBy(['route' => 'home/series']);
        $this->seo(
            'Серии',
            'Серии',
            $menu->getDescription(),
            $menu->getKeywords()
        );
        $vm = new ViewModel(
            [
                'paginator' => $paginator,
                'menu' => $menu
            ]
        );
        return $vm;
    }

    /**
     * @return void|ViewModel
     */
    public function seriesoneAction()
    {
        $page = $this->params()->fromRoute('page_series', 1);
        $alias_menu = $this->params()->fromRoute('alias_menu');
        if($this->params()->fromRoute('paged')){
            $this->getResponse()->setStatusCode(404);
            return;
        }
        if ($page == 1) {
            $this->noindex(false);
        } else {
            $this->noindex(true);
        }
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Repository\MSeriiRepository */
        $serii = $em->getRepository(MSerii::class)->findOneBy(['alias' => $alias_menu]);
        if(!$serii){
            $this->getResponse()->setStatusCode(404);
            return;
        }
        /** @var  $repository \Application\Repository\BookRepository */
        $repository = $em->getRepository(Book::class);
        $where = [
            'where' => [
                'ms_name' => [
                    'column'   => 'ms.alias',
                    'type'     => '=',
                    'value'    => $alias_menu,
                    'operator' => 'and',
                ],
            ],
        ];
        $query = $repository->getBooksQuery(
            $this->getServiceLocator()->get('arraySort'),
            $where
        );
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(27);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(6);
        $title = "Серия - ".$serii->getName();
        $this->seo($serii->getName(), $serii->getName());

        $vm = new ViewModel(
            [
                'paginator' => $paginator,
                'title' => $title
            ]
        );
        return $vm;
    }

    /**
     * @return void|ViewModel
     */
    public function translitAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Repository\MTranslitRepository */
        $repository = $em->getRepository(MTranslit::class);
        $query = $repository->getTranslits();
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(200);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(6);
        $menu = $em->getRepository(MZhanr::class)->findOneBy(['route' => 'home/translit']);
        $this->seo(
            'Переводчики',
            'Переводчики',
            $menu->getDescription(),
            $menu->getKeywords()
        );
        $vm = new ViewModel(
            [
                'paginator' => $paginator,
                'menu' => $menu
            ]
        );
        return $vm;
    }

    /**
     * @return void|ViewModel
     */
    public function translitoneAction()
    {
        $page = $this->params()->fromRoute('page_translit', 1);
        $alias_menu = $this->params()->fromRoute('alias_menu');
        if($this->params()->fromRoute('paged')){
            $this->getResponse()->setStatusCode(404);
            return;
        }
        if ($page == 1) {
            $this->noindex(false);
        } else {
            $this->noindex(true);
        }
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Repository\MTranslitRepository */
        $translit = $em->getRepository(MTranslit::class)->findOneBy(['alias' => $alias_menu]);
        if(!$translit){
            $this->getResponse()->setStatusCode(404);
            return;
        }
        /** @var  $repository \Application\Repository\BookRepository */
        $repository = $em->getRepository(Book::class);

        $where = [
            'where' => [
                'mt_name' => [
                    'column'   => 'mt.alias',
                    'type'     => '=',
                    'value'    => $alias_menu,
                    'operator' => 'and',
                ],
            ],
        ];
        $query = $repository->getBooksQuery(
            $this->getServiceLocator()->get('arraySort'),
            $where
        );
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(27);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(6);
        $title = "Переводчик - ".$translit->getName();
        $this->seo($translit->getName(), $translit->getName());
        $vm = new ViewModel(
            [
                'paginator' => $paginator,
                'title' => $title
            ]
        );
        return $vm;
    }

    /**
     * @return array
     */
    public function sitemapsAction()
    {
        return [];
    }

    /**
     * @return array
     */
    public function rightholderAction()
    {
        return [];
    }

    /**
     * @return void|\Zend\Http\Response|ViewModel
     */
    public function bookAction($type = 'genre')
    {
        $alias_book = $this->params()->fromRoute('book', null);
        if(!$alias_book){
            $this->getResponse()->setStatusCode(404);
            return;
        }
        $em = $this->getEntityManager();
        /** @var \Application\Entity\Book $bookEntity */
        $bookEntity = $em->getRepository(Book::class)->findOneBy(['alias' => $alias_book]);
        if(!$bookEntity or $this->params()->fromRoute('paged')){
            $this->getResponse()->setStatusCode(404);
            return;
        }
        if ($type != 'problem-avtor' and $bookEntity->getVis() == 0) {
            /** @var \Zend\Mvc\Controller\Plugin\Redirect $ridirect */
            $ridirect = $this->redirect();
            return  $ridirect
                    ->toUrl('/blocked-book/'.$bookEntity->getAlias().'/')
                    ->setStatusCode(301);
        }
        $bookEntity->setVisit($bookEntity->getVisit()+1);
        $em->persist($bookEntity);
        $em->flush($bookEntity);
        $title = $bookEntity->getMetaTitle($type);
        $problem_avtor = 0;
        switch($type){
            case 'genre': $similar = $em->getRepository(Book::class)->similar($bookEntity);
                $route_similar = 'home/genre/one/book';
                $this->seo(
                    $bookEntity->getName(),
                    $bookEntity->getName(),
                    $title,
                    $title
                );
                break;
            case 'serii':   $similar = $em->getRepository(Book::class)->similarSerii($bookEntity);
                $route_similar = 'home/series/one/book';
                $this->seo(
                    $bookEntity->getName().". Серия - ".$bookEntity->getSerii()->current()->getName(),
                    false,
                    $title,
                    $title
                );
                break;
            case 'avtor':   $similar = $em->getRepository(Book::class)->similarAvtor($bookEntity);
                $route_similar = 'home/authors/one/book';
                $this->seo(
                    $bookEntity->getName().". Автор - ".$bookEntity->getAvtor()->current()->getName(),
                    false,
                    $title,
                    $title
                );
                break;
            case 'translit':   $similar = $em->getRepository(Book::class)->similarTranslit($bookEntity);
                $route_similar = 'home/translit/one/book';
                $this->seo(
                    $bookEntity->getName().". Переводчик - ".$bookEntity->getTranslit()->current()->getName(),
                    false,
                    $title,
                    $title
                );
                break;
            case 'problem-avtor': $similar = $em->getRepository(Book::class)->similar($bookEntity);
                $route_similar = 'home/genre/one/book';
                $this->seo(
                    $bookEntity->getName(),
                    $bookEntity->getName(),
                    $title,
                    $title
                );
                $problem_avtor = 1;
                break;
        }
        $vm = new ViewModel(
            [
                'book'          => $bookEntity,
                'title'         => $title,
                'similar'       => $similar,
                'route_similar' => $route_similar,
                'problem_avtor' => $problem_avtor
            ]
        );
        $vm->setTemplate('application/index/book');
        return $vm;
    }

    /**
     * @return void|\Zend\Http\Response|ViewModel
     */
    public function sbookAction()
    {
        return $this->bookAction('serii');
    }

    /**
     * @return void|\Zend\Http\Response|ViewModel
     */
    public function abookAction()
    {
        return $this->bookAction('avtor');
    }

    /**
     * @return void|\Zend\Http\Response|ViewModel
     */
    public function tbookAction()
    {
        return $this->bookAction('translit');
    }

    /**
     * @return void|\Zend\Http\Response|ViewModel
     */
    public function problemAvtorAction()
    {
        return $this->bookAction('problem-avtor');
    }

    public function genreAction()
    {
        $sm = $this->getServiceLocator();
        $where = "route = 'home/genre'";
        $menu = $sm->get('Application\Model\MZhanrTable')->fetchAll(
            false,
            false,
            $where
        );
        $menu = $menu[0];
        $this->seo(
            "книга читать жанры онлайн бесплатно",
            "книга жанры онлайн бесплатно",
            $menu->description,
            $menu->keywords
        );

        return new ViewModel(
            [
                'menu' => $menu,
            ]
        );
    }

    public function readAction()
    {
        $sm = $this->getServiceLocator();
        $alias_book = $this->params()->fromRoute('book');
        $page_str = $this->params()->fromRoute('page_str', 0);


        $where = "book.alias = '$alias_book'";
        $book = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            $where
        );
        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $book = (array)$book[0];
        if ($book['vis'] == 0) {
            return $this->redirect()->toUrl('/blocked-book/'.$book['alias'].'/')
                ->setStatusCode(301);
        }
        if (!$page_str) {
            $t = "Книга ".$book['name'].". Страницы:";
            $this->seo(
                $book['name'].". Страницы ",
                $book['name'].". Страницы".$page_str,
                $t,
                $t
            );
            $vm = new ViewModel(['title' => $t]);
            $vm->setTemplate('application/index/zread');

            return $vm;
        }


        $where = "text.id_main = {$book['id']}";
        $text = $sm->get('Application\Model\TextTable')->fetchAll(
            true,
            false,
            $where
        );

        $text->setCurrentPageNumber((int)$page_str);
        $text->setItemCountPerPage(1);

        $t = "Книга ".$book['name'].". Страница ".$page_str;
        $this->seo(
            $book['name'].". Страница ".$page_str,
            $book['name'].". Страница ".$page_str,
            $t,
            $t
        );


        $vm = new ViewModel(
            [
                'book'  => $book,
                'text'  => $text,
                'title' => $t,
            ]
        );
        $vm->setTemplate('application/index/read_content');

        return $vm;
    }

    public function treadAction()
    {

        $sm = $this->getServiceLocator();
        $alias_book = $this->params()->fromRoute('book');
        $page_str = $this->params()->fromRoute('page_str', 0);
        $alias_menu = $this->params()->fromRoute('alias_menu');

        $where = "book.alias = '$alias_book'";
        $book = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            $where
        );
        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $book = (array)$book[0];
        if ($book['vis'] == 0) {
            return $this->redirect()->toUrl('/blocked-book/'.$book['alias'].'/')
                ->setStatusCode(301);
        }
        $where = "alias = '$alias_menu'";
        $translit = $sm->get('Application\Model\MTranslitTable')->fetchAll(
            false,
            false,
            $where
        )->current();
        if (!$page_str) {
            $t = "Книга ".$book['name'].". Переводчик ".$translit->name
                .". Страницы:";
            $this->seo(
                $book['name'].". Переводчик ".$translit->name.". Страницы",
                $book['name'].". Переводчик ".$translit->name.". Страницы",
                $t,
                $t
            );


            $vm = new ViewModel(['title' => $t]);
            $vm->setTemplate('application/index/zread');

            return $vm;
        }


        $where = "text.id_main = {$book['id']}";
        $text = $sm->get('Application\Model\TextTable')->fetchAll(
            true,
            false,
            $where
        );
        //var_dump($where);
        $text->setCurrentPageNumber((int)$page_str);
        $text->setItemCountPerPage(1);
        //var_dump($text->count());
        //var_dump(get_class_methods($text));die();

        $t = "Книга ".$book['name'].". Переводчик ".$translit->name
            .". Страница ".$page_str;
        $this->seo(
            $book['name'].". Переводчик ".$translit->name.". Страница "
            .$page_str,
            $book['name'].". Переводчик ".$translit->name.". Страница "
            .$page_str,
            $t,
            $t
        );


        $vm = new ViewModel(
            [
                'book'  => $book,
                'text'  => $text,
                'title' => $t,
            ]
        );

        $vm->setTemplate('application/index/read_content');

        return $vm;

    }

    public function areadAction()
    {

        $sm = $this->getServiceLocator();
        $alias_book = $this->params()->fromRoute('book');
        $page_str = $this->params()->fromRoute('page_str', 0);
        $alias_menu = $this->params()->fromRoute('alias_menu');

        $where = "book.alias = '$alias_book'";
        $book = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            $where
        );
        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $book = (array)$book[0];
        if ($book['vis'] == 0) {
            return $this->redirect()->toUrl('/blocked-book/'.$book['alias'].'/')
                ->setStatusCode(301);
        }
        $where = "alias = '$alias_menu'";
        $avtor = $sm->get('Application\Model\MAvtorTable')->fetchAll(
            false,
            false,
            $where
        );

        if ($avtor->count() == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }

        $avtor = $avtor->current();

        if (!$page_str) {
            $t = "Книга ".$book['name'].". Автор ".$avtor->name.". Страницы:";
            $this->seo(
                $book['name'].". Автор ".$avtor->name.". Страницы",
                $book['name'].". Автор ".$avtor->name.". Страницы",
                $t,
                $t
            );


            $vm = new ViewModel(['title' => $t]);
            $vm->setTemplate('application/index/zread');

            return $vm;
        }


        $where = "text.id_main = {$book['id']}";
        $text = $sm->get('Application\Model\TextTable')->fetchAll(
            true,
            false,
            $where
        );
        //var_dump($where);
        $text->setCurrentPageNumber((int)$page_str);
        $text->setItemCountPerPage(1);
        //var_dump($text->count());
        //var_dump(get_class_methods($text));die();

        $t = "Книга ".$book['name'].". Автор ".$avtor->name.". Страница "
            .$page_str;
        $this->seo(
            $book['name'].". Автор ".$avtor->name.". Страница ".$page_str,
            $book['name'].". Автор ".$avtor->name.". Страница ".$page_str,
            $t,
            $t
        );


        $vm = new ViewModel(
            [
                'book'  => $book,
                'text'  => $text,
                'title' => $t,
            ]
        );

        $vm->setTemplate('application/index/read_content');

        return $vm;

    }

    public function sreadAction()
    {

        $sm = $this->getServiceLocator();
        $alias_book = $this->params()->fromRoute('book');
        $page_str = $this->params()->fromRoute('page_str', 0);
        $alias_menu = $this->params()->fromRoute('alias_menu');
        $where = "book.alias = '$alias_book'";
        $book = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            $where
        );
        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $book = (array)$book[0];
        if ($book['vis'] == 0) {
            return $this->redirect()->toUrl('/blocked-book/'.$book['alias'].'/')
                ->setStatusCode(301);
        }
        $where = "alias = '$alias_menu'";

        $serii = $sm->get('Application\Model\MSeriiTable')->fetchAll(
            false,
            false,
            $where
        );

        if ($serii->count() == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }

        $serii = $serii->current();

        if (!$page_str) {

            $t = "Книга ".$book['name'].". Серия ".$serii->name.". Страницы: ";
            $this->seo(
                $book['name'].". Серия ".$serii->name.". Страницы",
                $book['name'].". Серия ".$serii->name.". Страницы",
                $t,
                $t
            );

            $vm = new ViewModel(['title' => $t]);
            $vm->setTemplate('application/index/zread');

            return $vm;
        }


        $where = "text.id_main = {$book['id']}";
        $text = $sm->get('Application\Model\TextTable')->fetchAll(
            true,
            false,
            $where
        );
        //var_dump($where);
        $text->setCurrentPageNumber((int)$page_str);
        $text->setItemCountPerPage(1);

        $t = "Книга ".$book['name'].". Серия ".$serii->name.". Страница "
            .$page_str;
        $this->seo(
            $book['name'].". Серия ".$serii->name.". Страница ".$page_str,
            $book['name'].". Серия ".$serii->name.". Страница ".$page_str,
            $t,
            $t
        );


        $vm = new ViewModel(
            [
                'book'  => $book,
                'text'  => $text,
                'title' => $t,
            ]
        );

        $vm->setTemplate('application/index/read_content');

        return $vm;

    }

    public function contentAction()
    {

        $sm = $this->getServiceLocator();
        $alias_book = $this->params()->fromRoute('book');
        $alias_content = $this->params()->fromRoute('content');
        $where = "book.alias = '$alias_book'";
        $book = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            $where
        );
        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $book = (array)$book[0];
        if ($book['vis'] == 0) {
            return $this->redirect()->toUrl('/blocked-book/'.$book['alias'].'/')
                ->setStatusCode(301);
        }
        if (!$alias_content) {
            $t = "Книга ".$book['name'].". Содержание:";
            $this->seo(
                $book['name']." - Содержание",
                $book['name']." - Содержание",
                $t,
                $t
            );
            $vm = new ViewModel(['title' => $t]);
            $vm->setTemplate('application/index/zcontent');

            return $vm;
        }

        $where = "soder.id_main = '{$book['id']}' and soder.alias = '$alias_content'";
        $soder = $sm->get('Application\Model\SoderTable')->fetchAll(
            false,
            false,
            $where
        )->current();
        $where = "text.id_main = {$book['id']}";
        $text = $sm->get('Application\Model\TextTable')->fetchAll(
            true,
            false,
            $where
        );
        if (!isset($soder->name)) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $text->setCurrentPageNumber((int)$soder->num);
        $text->setItemCountPerPage(1);

        $t = "Книга ".$book['name'].". Содержание - ".$soder->name;
        $this->seo(
            $book['name']." - ".$soder->name,
            $book['name']." - ".$soder->name,
            $t,
            $t
        );


        $vm = new ViewModel(
            [
                'book'  => $book,
                'text'  => $text,
                'title' => $t,
                'route' => 'home/genre/one/book/read',
            ]
        );

        $vm->setTemplate('application/index/read_content');

        return $vm;

    }

    public function tcontentAction()
    {
        $sm = $this->getServiceLocator();
        $alias_book = $this->params()->fromRoute('book');
        $alias_content = $this->params()->fromRoute('content');
        $where = "book.alias = '$alias_book'";
        $book = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            $where
        );
        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $book = (array)$book[0];
        if ($book['vis'] == 0) {
            return $this->redirect()->toUrl('/blocked-book/'.$book['alias'].'/')
                ->setStatusCode(301);
        }
        $alias_menu = $this->params()->fromRoute('alias_menu');
        $where = "alias = '$alias_menu'";
        $translit = $sm->get('Application\Model\MTranslitTable')->fetchAll(
            false,
            false,
            $where
        );

        if ($translit->count() == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $translit = $translit->current();

        if (!$alias_content) {
            $t = "Книга ".$book['name'].". Переводчик ".$translit->name
                .". Содержание:";
            $this->seo(
                $book['name'].". Переводчик ".$translit->name.". Содержание",
                $book['name'].". Переводчик ".$translit->name.". Содержание",
                $t,
                $t
            );
            $vm = new ViewModel(['title' => $t]);
            $vm->setTemplate('application/index/zcontent');

            return $vm;
        }

        $where = "soder.id_main = '{$book['id']}' and soder.alias = '$alias_content'";
        $soder = $sm->get('Application\Model\SoderTable')->fetchAll(
            false,
            false,
            $where
        )->current();

        if (!isset($soder->name)) {
            $this->getResponse()->setStatusCode(404);

            return;
        }

        $where = "text.id_main = {$book['id']}";
        $text = $sm->get('Application\Model\TextTable')->fetchAll(
            true,
            false,
            $where
        );
        $text->setCurrentPageNumber((int)$soder->num);
        $text->setItemCountPerPage(1);

        $t = "Книга ".$book['name'].". Переводчик ".$translit->name
            .". Содержание - ".$soder->name;
        $this->seo(
            $book['name'].". Переводчик ".$translit->name.". Содержание - "
            .$soder->name,
            $book['name'].". Переводчик ".$translit->name.". Содержание - "
            .$soder->name,
            $t,
            $t
        );

        $vm = new ViewModel(
            [
                'book'  => $book,
                'text'  => $text,
                'title' => $t,
                'route' => 'home/translit/one/book/read',
            ]
        );

        $vm->setTemplate('application/index/read_content');

        return $vm;
    }

    public function acontentAction()
    {
        $sm = $this->getServiceLocator();
        $alias_book = $this->params()->fromRoute('book');
        $alias_content = $this->params()->fromRoute('content');
        $where = "book.alias = '$alias_book'";
        $book = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            $where
        );
        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $book = (array)$book[0];
        if ($book['vis'] == 0) {
            return $this->redirect()->toUrl('/blocked-book/'.$book['alias'].'/')
                ->setStatusCode(301);
        }
        $alias_menu = $this->params()->fromRoute('alias_menu');
        $where = "alias = '$alias_menu'";
        $avtor = $sm->get('Application\Model\MAvtorTable')->fetchAll(
            false,
            false,
            $where
        );

        if ($avtor->count() == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $avtor = $avtor->current();

        if (!$alias_content) {
            $t = "Книга ".$book['name'].". Автор ".$avtor->name.". Содержание:";
            $this->seo(
                $book['name'].". Автор ".$avtor->name.". Содержание",
                $book['name'].". Автор ".$avtor->name.". Содержание",
                $t,
                $t
            );
            $vm = new ViewModel(['title' => $t]);
            $vm->setTemplate('application/index/zcontent');

            return $vm;
        }

        $where = "soder.id_main = '{$book['id']}' and soder.alias = '$alias_content'";
        $soder = $sm->get('Application\Model\SoderTable')->fetchAll(
            false,
            false,
            $where
        )->current();

        if (!isset($soder->name)) {
            $this->getResponse()->setStatusCode(404);

            return;
        }

        $where = "text.id_main = {$book['id']}";
        $text = $sm->get('Application\Model\TextTable')->fetchAll(
            true,
            false,
            $where
        );
        $text->setCurrentPageNumber((int)$soder->num);
        $text->setItemCountPerPage(1);

        $t = "Книга ".$book['name'].". Автор ".$avtor->name.". Содержание - "
            .$soder->name;
        $this->seo(
            $book['name'].". Автор ".$avtor->name.". Содержание - "
            .$soder->name,
            $book['name'].". Автор ".$avtor->name.". Содержание - "
            .$soder->name,
            $t,
            $t
        );

        $vm = new ViewModel(
            [
                'book'  => $book,
                'text'  => $text,
                'title' => $t,
                'route' => 'home/authors/one/book/read',
            ]
        );

        $vm->setTemplate('application/index/read_content');

        return $vm;
    }

    public function scontentAction()
    {

        $sm = $this->getServiceLocator();
        $alias_book = $this->params()->fromRoute('book');
        $alias_content = $this->params()->fromRoute('content');
        $alias_menu = $this->params()->fromRoute('alias_menu');
        $where = "book.alias = '$alias_book'";
        $book = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            $where
        );
        if (count($book) == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $book = (array)$book[0];
        if ($book['vis'] == 0) {
            return $this->redirect()->toUrl('/blocked-book/'.$book['alias'].'/')
                ->setStatusCode(301);
        }
        $where = "alias = '$alias_menu'";
        $serii = $sm->get('Application\Model\MSeriiTable')->fetchAll(
            false,
            false,
            $where
        );
        if ($serii->count() == 0) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $serii = $serii->current();

        if (!$alias_content) {
            $t = "Книга ".$book['name'].". Серия ".$serii->name.". Содержание:";
            $this->seo(
                $book['name'].". Серия ".$serii->name.". Содержание.",
                $book['name'].". Серия ".$serii->name.". Содержание.",
                $t,
                $t
            );
            $vm = new ViewModel(['title' => $t]);
            $vm->setTemplate('application/index/zcontent');

            return $vm;
        }

        $where = "soder.id_main = '{$book['id']}' and soder.alias = '$alias_content'";
        $soder = $sm->get('Application\Model\SoderTable')->fetchAll(
            false,
            false,
            $where
        )->current();


        if (!isset($soder->name)) {
            $this->getResponse()->setStatusCode(404);

            return;
        }
        $where = "text.id_main = {$book['id']}";
        $text = $sm->get('Application\Model\TextTable')->fetchAll(
            true,
            false,
            $where
        );
        $text->setCurrentPageNumber((int)$soder->num);
        $text->setItemCountPerPage(1);


        $t = "Книга ".$book['name'].". Серия ".$serii->name.". Содержание - "
            .$soder->name;
        $this->seo(
            $book['name'].". Серия ".$serii->name.". Содержание - "
            .$soder->name,
            $book['name'].". Серия ".$serii->name.". Содержание - "
            .$soder->name,
            $t,
            $t
        );


        $vm = new ViewModel(
            [
                'book'  => $book,
                'text'  => $text,
                'title' => $t,
                'route' => 'home/series/one/book/read',
            ]
        );

        $vm->setTemplate('application/index/read_content');

        return $vm;
    }


    /**
     * @param $search
     *
     * @return ViewModel
     */
    public function notSearch($search)
    {

        $vm = new ViewModel(['search' => $search]);
        $vm->setTemplate('application/index/notsearch');

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
        $renderer = $this->getServiceLocator()->get(
            'Zend\View\Renderer\PhpRenderer'
        );
        $renderer->headTitle($title);
        $renderer->headMeta()->appendName('description', $discription);
        $renderer->headMeta()->appendName('keywords', $keywords);

    }

    /**
     * @return JsonModel
     */
    public function starsAction()
    {

        $sm = $this->getServiceLocator();
        $arr = [];
        $stars = $this->params()->fromQuery('stars');
        $id_book = $this->params()->fromQuery('id_book');
        $ip = $this->getIp();
        $arr['stars'] = $stars;
        $arr['ip'] = $ip;
        $arr['id_book'] = $id_book;
        $err = 1;


        try {

            $check = $sm->get('Application\Model\StarsTable')->fetchAll(
                false,
                false,
                [
                    'id_book' => $id_book,
                    'ip'      => $ip,
                ]
            );

            if ($check->count() == 0) {
                $sm->get('Application\Model\StarsTable')->save($arr);

            } else {
                $sm->get('Application\Model\StarsTable')->save(
                    $arr,
                    [
                        'id_book' => $id_book,
                        'ip'      => $ip,
                    ]
                );
            }

            $stars = $sm->get('Application\Model\StarsTable')->fetchAll(
                false,
                false,
                ['id_book' => $id_book]
            );


            $num_stars = 0;
            $count = 0;
            foreach ($stars as $v) {
                $count++;
                $num_stars += $v->stars;

            }

            $aver_value = (float)($num_stars / $count);

            $arr = [];
            $arr['stars'] = $aver_value;
            $arr['count_stars'] = $count;
            $err = 0;
            $sm->get('Application\Model\BookTable')->save(
                $arr,
                ['id' => $id_book]
            );

        } catch (\Exception $e) {
            //TODO
        }

        return new JsonModel(
            [
                'stars' => $aver_value,
                'count' => $count,
                'err'   => $err,
            ]
        );

    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    /**
     * @param bool $n
     */
    public function noindex($n = true)
    {
        $renderer = $this->getServiceLocator()->get(
            'Zend\View\Renderer\PhpRenderer'
        );
        if ($n) {

            $renderer->headMeta()->appendName('ROBOTS', 'NOINDEX,FOLLOW');
        } else {
            $renderer->headMeta()->appendName('ROBOTS', 'INDEX,FOLLOW');
        }
    }
}
