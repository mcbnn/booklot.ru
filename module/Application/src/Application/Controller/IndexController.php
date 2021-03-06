<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Entity\BookFiles;
use Application\Entity\BookNotes;
use Application\Traits\Main;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Application\Entity\Book;
use Application\Entity\MZhanr;
use Application\Entity\Ad;
use Application\Entity\AdStat;
use Application\Entity\MAvtor;
use Application\Entity\MSerii;
use Application\Entity\MTranslit;
use Application\Entity\Text;
use Application\Entity\Soder;
use Application\Entity\Stars;
use Zend\Http\Response;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator as ZendPaginator;

class IndexController extends AbstractActionController
{
    use Main;
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

    public function testEmailAction()
    {

        $title = "Регистрация на сайте booklot.org, код подтверждения";
        $to = 'booklot@yandex.ru';
        $from = "postmaster@my.booklot.org";
        $confirm = rand(11111, 2222222);
        $html = '<h1>Спасибо ' . $confirm . '</h1>';

        //$mg = $this->sm->get('email4');

        try
        {
	        $mainController = new MainController();
	        $mainController->email4('gmail', $title, $to, $from, $html);
//            $mg->messages()->send('my.booklot.org', [
//                'from' => $from,
//                'to' => $to,
//                'subject' => $title,
//                'html' => $html
//            ]);
        } catch ( \Exception $e )
        {
            var_dump($e->getMessage());
        }
        die();
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        if ($page == 1 || $page == "") {
            $this->noindex(false);
        } else {
            $this->noindex(true);
        }

        $this->seo(
            'Главная страница',
            'Главная страница',
            'книги скачать бесплатно читать онлайн новинки 2017 2018 2019 2020 года будущая и прошедшая литература',
            'Книга, скачать, бесплатно, dwonload book, большой выбор, литература, фантастика, жанры, большой выбор'
        );
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Repository\BookRepository */
        $repository = $em->getRepository(Book::class);
        $where = [
            'where' => [
                'b_vis' => [
                    'column'   => 'b.vis',
                    'type'     => '=',
                    'value'    => 1,
                    'operator' => 'and',
                ],
                'b_ban' => [
                    'column'   => 'b.ban',
                    'type'     => '=',
                    'value'    => 0,
                    'operator' => 'and',
                ],
            ],
        ];
        $sm = $this->sm;
        $query = $repository->getBooksQuery(
            $sm->get('arraySort'),
            $where
        );
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(24);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(6);
        $vm = new ViewModel(
            [
                'paginator' => $paginator,
                'route'     => 'home',
                'params' => $this->params()->fromRoute(),
            ]
        );
        return $vm;
    }

    public function notesAction()
    {
        $book_id = $this->params()->fromPost('book_id');
        $link = $this->params()->fromPost('link');
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Entity\BookNotes */
        $notes = $em->getRepository(BookNotes::class)->findOneBy(['book' => $book_id, 'link' => $link]);
        $json = ['err' => 1, 'text' => 'Возникла ошибка'];
        if($notes){
            $json = ['err' => 0, 'text' => $notes->getText(), 'title' => $notes->getTitle()];
        }
        echo json_encode($json);
        exit();
    }

    /**
     *
     */
    public function downloadAction()
    {
        $sec = 1200;
        $id_book_files = $this->params()->fromRoute('id_book_files');
        $timestamp = $this->params()->fromRoute('timestamp');
        $config = $this->sm->get('Config');
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Entity\BookFiles */
        $repository = $em->getRepository(BookFiles::class);
        $file = $repository->find($id_book_files);
        $url =  '/var/www/booklot/public/templates/newsave/'.$file->getFileUrl();
        $book = $file->getIdBook();
        if(time() - $timestamp <= 0 || time() - $timestamp >= $sec || !file_exists($config['UPLOAD_DIR'].'newsave/'.$file->getFileUrl()) || !$book->getVis() ||  $book->getBan()) {
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file->getFileUrl() . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($url)); //Absolute URL
        ob_clean();
        flush();
        readfile($url);
        exit();
    }

    /**
     * @return ViewModel
     */
    public function adIframeAction()
    {
        $vm = new ViewModel();
        $vm->setTemplate('application/ad/iframe');
        $vm->setTerminal(true);
        return $vm;
    }

    /**
     * @return ViewModel
     */
    public function adIframe2Action()
    {
        $vm = new ViewModel();
        $vm->setTemplate('application/ad/iframe2');
        $vm->setTerminal(true);
        return $vm;
    }

    /**
     * @return ViewModel
     */
    public function adIframe3Action()
    {
        $vm = new ViewModel();
        $vm->setTemplate('application/ad/iframe3');
        $vm->setTerminal(true);
        return $vm;
    }

    /**
     * @return ViewModel
     */
    public function adIframe4Action()
    {
        $vm = new ViewModel();
        $vm->setTemplate('application/ad/iframe4');
        $vm->setTerminal(true);
        return $vm;
    }

    /**
     * @return ViewModel
     */
    public function adIframe5Action()
    {
        $vm = new ViewModel();
        $vm->setTemplate('application/ad/iframe5');
        $vm->setTerminal(true);
        return $vm;
    }

    /**
     * @return void|JsonModel
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function adStatAddAction()
    {
        $em = $this->getEntityManager();
        $ad_id = $this->params()->fromPost('ad_id');
        $page = $this->params()->fromPost('page');
        if(!$ad_id)return;
        if(!$page)return;
        $ip = $this->getIp();
        $ad = $em->getRepository(Ad::class)->find($ad_id);
        if(!$ad)return;

        $adStatCheck = $em->getRepository(AdStat::class)
            ->findOneBy(
                ['info' => $ip, 'ad' => $ad_id],
                ['datetime' => 'desc']
            );

        if($adStatCheck and
            time() - strtotime($adStatCheck->getDatetime()->format('Y-m-d H:i:s')) < 10)return;
        /** @var \Application\Entity\AdStat $adStat */
        $adStat = new AdStat();
        $adStat->setAd($ad);
        $adStat->setDatetime(new \DateTime('now'));
        $adStat->setInfo( $ip );
        $adStat->setPage($page);
        $em->persist($adStat);
        $em->flush();
        return new JsonModel(['success' => 1, 'errors' => false]);
    }

    /**
     * @return ViewModel|Response
     */
    public function oneGenreAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        $alias_menu = strtolower($this->params()->fromRoute('alias_menu'));
        $ns = strtolower($this->params()->fromRoute('s', null));
        if($this->params()->fromRoute('paged')){
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Repository\MZhanrRepository */
        $mzhanr = $em->getRepository(MZhanr::class)->findByAliasCheckParentZhanr($alias_menu);
        if ($ns && !$em->getRepository(MZhanr::class)->findByAliasCheckParentZhanr($ns)) {
            $mzhanr = false;
        }
        if (!$mzhanr) {
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }
        if ($page == 1 || $page == "") {
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
                ],
                'b_vis' => [
                    'column'   => 'b.vis',
                    'type'     => '=',
                    'value'    => 1,
                    'operator' => 'and',
                ],
                'b_ban' => [
                    'column'   => 'b.ban',
                    'type'     => '=',
                    'value'    => 0,
                    'operator' => 'and',
                ],
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
                'b_vis' => [
                    'column'   => 'b.vis',
                    'type'     => '=',
                    'value'    => 1,
                    'operator' => 'and',
                ],
                'b_ban' => [
                    'column'   => 'b.ban',
                    'type'     => '=',
                    'value'    => 0,
                    'operator' => 'and',
                ],
            ]
            ];
        }
        $sm = $this->sm;
        $query = $repository->getBooksQuery(
            $sm->get('arraySort'),
            $where
        );
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(24);
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
                'zhanr'     => $mzhanr,
                'params' => $this->params()->fromRoute(),
                'route' => 'home/genre/one',
            ]
        );
        return $vm;
    }

    /**
     * @return JsonModel
     */
    public function ajaxsearchAction()
    {
        $sm = $this->sm;
        $dataBase = $sm->get('AjaxSearch');
        return new JsonModel($dataBase);
    }

    /**
     * @return ViewModel
     */
    public function searchAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        if ($page == 1 || $page == "") {
            $this->noindex(false);
        } else {
            $this->noindex(true);
        }
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Repository\BookRepository */
        $repository = $em->getRepository(Book::class);
        $sm = $this->sm;

        $query = $repository->getBooksQuery(
            $sm->get('arraySort'),
            $sm->get('arrayWhere')
        );
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(24);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(6);
        $vm = new ViewModel(
            [
                'paginator' => $paginator,
                'params' => $this->params()->fromRoute(),
                'route' => 'home/search',
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
        $paginator->setDefaultItemCountPerPage(50);
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
                'menu' => $menu,
                'params' => $this->params()->fromRoute(),
                'route' => 'home/authors',
            ]
        );
        return $vm;
    }

    /**
     * @return Response|ViewModel
     */
    public function authorAction()
    {
        $page = $this->params()->fromRoute('page_author', 1);
        $alias_menu = strtolower($this->params()->fromRoute('alias_menu'));
        if($this->params()->fromRoute('paged')){
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }
        if ($page == 1 || $page == "") {
            $this->noindex(false);
        } else {
            $this->noindex(true);
        }
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Repository\MAvtorRepository */
        $avtor = $em->getRepository(MAvtor::class)->findOneBy(['alias' => $alias_menu]);
        if(!$avtor){
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
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
                'b_vis' => [
                    'column'   => 'b.vis',
                    'type'     => '=',
                    'value'    => 1,
                    'operator' => 'and',
                ],
                'b_ban' => [
                    'column'   => 'b.ban',
                    'type'     => '=',
                    'value'    => 0,
                    'operator' => 'and',
                ],
            ],
        ];
        $sm = $this->sm;
        $query = $repository->getBooksQuery(
            $sm->get('arraySort'),
            $where
        );
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(24);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(6);
        $title = "Автор - ".$avtor->getName();
        $this->seo($avtor->getName(), $avtor->getName());

        $vm = new ViewModel(
            [
                'paginator' => $paginator,
                'title' => $title,
                'params' => $this->params()->fromRoute(),
                'route' => 'home/authors/one',
            ]
        );
        return $vm;
    }

    /**
     * @return ViewModel
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
        $paginator->setDefaultItemCountPerPage(50);
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
                'menu' => $menu,
                'params' => $this->params()->fromRoute(),
                'route' => 'home/series',
            ]
        );
        return $vm;
    }

    /**
     * @return Response|ViewModel
     */
    public function seriesoneAction()
    {
        $page = $this->params()->fromRoute('page_series', 1);
        $alias_menu = strtolower($this->params()->fromRoute('alias_menu'));
        if($this->params()->fromRoute('paged')){
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }
        if ($page == 1 || $page == "") {
            $this->noindex(false);
        } else {
            $this->noindex(true);
        }
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Repository\MSeriiRepository */
        $serii = $em->getRepository(MSerii::class)->findOneBy(['alias' => $alias_menu]);
        if(!$serii){
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
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
                'b_vis' => [
                    'column'   => 'b.vis',
                    'type'     => '=',
                    'value'    => 1,
                    'operator' => 'and',
                ],
                'b_ban' => [
                    'column'   => 'b.ban',
                    'type'     => '=',
                    'value'    => 0,
                    'operator' => 'and',
                ],
            ],
        ];
        $sm = $this->sm;
        $query = $repository->getBooksQuery(
            $sm->get('arraySort'),
            $where
        );
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(24);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(6);
        $title = "Серия - ".$serii->getName();
        $this->seo($serii->getName(), $serii->getName());

        $vm = new ViewModel(
            [
                'paginator' => $paginator,
                'title' => $title,
                'params' => $this->params()->fromRoute(),
                'route' => 'home/series/one',
            ]
        );
        return $vm;
    }

    /**
     * @return Response|ViewModel
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
        $paginator->setDefaultItemCountPerPage(50);
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
                'menu' => $menu,
                'params' => $this->params()->fromRoute(),
                'route' => 'home/translit',
            ]
        );
        return $vm;
    }

    /**
     * @return Response|ViewModel
     */
    public function translitoneAction()
    {
        $page = $this->params()->fromRoute('page_translit', 1);
        $alias_menu = strtolower($this->params()->fromRoute('alias_menu'));
        if($this->params()->fromRoute('paged')){
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }
        if ($page == 1 || $page == "") {
            $this->noindex(false);
        } else {
            $this->noindex(true);
        }
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Repository\MTranslitRepository */
        $translit = $em->getRepository(MTranslit::class)->findOneBy(['alias' => $alias_menu]);
        if(!$translit){
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
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
                'b_vis' => [
                    'column'   => 'b.vis',
                    'type'     => '=',
                    'value'    => 1,
                    'operator' => 'and',
                ],
                'b_ban' => [
                    'column'   => 'b.ban',
                    'type'     => '=',
                    'value'    => 0,
                    'operator' => 'and',
                ],
            ],
        ];
        $sm = $this->sm;
        $query = $repository->getBooksQuery(
            $sm->get('arraySort'),
            $where
        );
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(24);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(6);
        $title = "Переводчик - ".$translit->getName();
        $this->seo($translit->getName(), $translit->getName());
        $vm = new ViewModel(
            [
                'paginator' => $paginator,
                'title' => $title,
                'params' => $this->params()->fromRoute(),
                'route' => 'home/translit',
            ]
        );
        return $vm;
    }

    /**
     * @param string $type
     *
     * @return Response|ViewModel
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function bookAction($type = 'genre')
    {
        $em = $this->getEntityManager();
        $alias_book = strtolower($this->params()->fromRoute('book', null));
        $ns = strtolower($this->params()->fromRoute('s', null));
        if ($ns && !$em->getRepository(MZhanr::class)->findByAliasCheckParentZhanr($ns)) {
            $alias_book = false;
        }
        if(!$alias_book){
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }
        /** @var \Application\Entity\Book $bookEntity */
        $bookEntity = $em->getRepository(Book::class)->findOneByRep(['alias' => $alias_book, 'vis' => 1]);
        if(!$bookEntity or $this->params()->fromRoute('paged')){
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }
        $sm = $this->sm;
        if (
            $type != 'problem-avtor' and
            $bookEntity->getBan() == 1
            and $sm->get('User')->getRole() != 'admin'
        ) {
            /** @var \Zend\Mvc\Controller\Plugin\Redirect $ridirect */
            $ridirect = $this->redirect();
            return  $ridirect
                ->toUrl('/blocked-book/'.$bookEntity->getAlias().'/')
                ->setStatusCode(301);
        }
        $bookEntity->setVisit($bookEntity->getVisit()+1);
        $em->persist($bookEntity);
        $em->flush($bookEntity);
        $problem_avtor = 0;
        $route_similar = "";
        $similar = "";
        switch($type){
            case 'genre':
                /** @var  $repository \Application\Repository\BookRepository */
                $repository = $em->getRepository(Book::class);
                $similar = $repository->similar($bookEntity);
                $route_similar = 'home/genre/one/book';
                $title = "Книга ".$bookEntity->getName().". Жанр - ".$bookEntity->getMenu()
                        ->getParent()
                        ->getName()." - ".$bookEntity->getMenu()
                        ->getName();
                $this->seo(
                    $bookEntity->getName(),
                    $bookEntity->getName(),
                    $title,
                    $title
                );
                break;
            case 'serii':
                $serii = $this->getSerii($bookEntity);
                if(!$serii){
                    /** @var \Zend\Http\Response $response */
                    $response = new Response();
                    $response->setStatusCode(Response::STATUS_CODE_404);
                    return $response;
                }
                /** @var  $repository \Application\Repository\BookRepository */
                $repository = $em->getRepository(Book::class);
                $similar = $repository->similarSerii($bookEntity, $serii);
                $route_similar = 'home/series/one/book';
                $title = "Книга ".$bookEntity->getName().". Серия - ".$serii->getName();
                $this->seo(
                    $bookEntity->getName().". Серия - ".$serii->getName(),
                    false,
                    $title,
                    $title
                );
                break;
            case 'avtor':
                $avtor = $this->getAvtor($bookEntity);
                if(!$avtor){
                    /** @var \Zend\Http\Response $response */
                    $response = new Response();
                    $response->setStatusCode(Response::STATUS_CODE_404);
                    return $response;
                }
                /** @var  $repository \Application\Repository\BookRepository */
                $repository = $em->getRepository(Book::class);
                $similar = $repository->similarAvtor($bookEntity, $avtor);
                $route_similar = 'home/authors/one/book';
                $title = "Книга ".$bookEntity->getName().". Автор - ".$avtor->getName();
                $this->seo(
                    $bookEntity->getName().". Автор - ".$avtor->getName(),
                    false,
                    $title,
                    $title
                );
                break;
            case 'translit':
                $translit = $this->getTranslit($bookEntity);
                if(!$translit){
                    /** @var \Zend\Http\Response $response */
                    $response = new Response();
                    $response->setStatusCode(Response::STATUS_CODE_404);
                    return $response;
                }
                /** @var  $repository \Application\Repository\BookRepository */
                $repository = $em->getRepository(Book::class);

                $similar = $repository->similarTranslit($bookEntity, $translit);
                $route_similar = 'home/translit/one/book';
                $title = "Книга ".$bookEntity->getName().". Переводчик - ".$translit->getName();
                $this->seo(
                    $bookEntity->getName().". Переводчик - ".$translit->getName(),
                    false,
                    $title,
                    $title
                );
                break;
            case 'problem-avtor':
                /** @var  $repository \Application\Repository\BookRepository */
                $repository = $em->getRepository(Book::class);
                $similar = $repository->similar($bookEntity);
                $route_similar = 'home/genre/one/book';
                $title = "Книга ".$bookEntity->getName().". Жанр - ".$bookEntity->getMenu()
                        ->getParent()
                        ->getName()." - ".$bookEntity->getMenu()
                        ->getName();
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
                'problem_avtor' => $problem_avtor,
                'params'        => $this->params()->fromRoute()
            ]
        );
        $vm->setTemplate('application/index/book');
        return $vm;
    }

    /**
     * @return ViewModel
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function sbookAction()
    {
        return $this->bookAction('serii');
    }

    /**
     * @return ViewModel
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function abookAction()
    {
        return $this->bookAction('avtor');
    }

    /**
     * @return ViewModel
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function tbookAction()
    {
        return $this->bookAction('translit');
    }

    /**
     * @return ViewModel
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function problemAvtorAction()
    {
        return $this->bookAction('problem-avtor');
    }

    /**
     * @return array|null
     */
    public function sitemapsAction()
    {
        return [];
    }

    /**
     * @return array|null
     */
    public function rightholderAction()
    {
        return [];
    }

    /**
     * @return ViewModel
     */
    public function genreAction()
    {
        $em = $this->getEntityManager();
        $menu = $em->getRepository(MZhanr::class)->findOneBy(['route' => 'home/genre']);
        $this->seo(
            "книга читать жанры онлайн бесплатно",
            "книга жанры онлайн бесплатно",
            $menu->getDescription(),
            $menu->getKeywords()
        );
        return new ViewModel(
            [
                'menu' => $menu,
            ]
        );
    }

    /**
     * @return Response|ViewModel
     * @throws \Doctrine\ORM\ORMException
     */
    public function readAction()
    {
        $em = $this->getEntityManager();
        $alias_book = strtolower($this->params()->fromRoute('book'));
        $page_str = strtolower($this->params()->fromRoute('page_str', 0));
        /** @var \Application\Entity\Book $book */
        $book = $em->getRepository(Book::class)->findOneByRep(['alias' => $alias_book, 'vis' => 1]);
        if (!$book) {
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }
        $sm = $this->sm;
        if ($book->getBan() == 1  and $sm->get('User')->getRole() != 'admin') {
            return $this->redirect()->toUrl('/blocked-book/'.$book->getAlias().'/')
                ->setStatusCode(301);
        }
        if (!$page_str) {
            $title = "Книга ".$book->getName().". Страницы:";
            $this->seo(
                $book->getName().". Страницы ",
                $book->getName().". Страницы",
                $title,
                $title
            );
            $vm = new ViewModel(['title' => $title]);
            $vm->setTemplate('application/index/zread');
            return $vm;
        }
        $title = "Книга ".$book->getName().". Страница ".$page_str;
        $this->seo(
            $book->getName().". Страница ".$page_str,
            $book->getName().". Страница ".$page_str,
            $title,
            $title
        );

        /** @var  $repository \Application\Repository\TextRepository */
        $repository = $em->getRepository(Text::class);
        $query = $repository->getTexts($book->getId());
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(1);
        $paginator->setCurrentPageNumber($page_str);
        $paginator->setPageRange(6);
        $vm = new ViewModel(
            [
                'book'  => $book,
                'paginator'  => $paginator,
                'title' => $title,
                'params' => $this->params()->fromRoute(),
                'route' => 'home/genre/one/book/read',
            ]
        );
        $vm->setTemplate('application/index/read_content');
        return $vm;
    }

    /**
     * @return Response|ViewModel
     * @throws \Doctrine\ORM\ORMException
     */
    public function contentAction()
    {
        $em = $this->getEntityManager();
        $alias_book = strtolower($this->params()->fromRoute('book'));
        $alias_content = strtolower($this->params()->fromRoute('content'));
        /** @var \Application\Entity\Book $book */
        $book = $em->getRepository(Book::class)->findOneByRep(['alias' => $alias_book, 'vis' => 1]);
        if (!$book) {
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }
        $sm = $this->sm;
        if ($book->getBan() == 1  and $sm->get('User')->getRole() != 'admin') {
            return $this->redirect()->toUrl('/blocked-book/'.$book->getAlias().'/')
                ->setStatusCode(301);
        }
        /** @var \Application\Entity\Soder $soder */
        $soder = $em->getRepository(Soder::class)->findOneBy(
            [
                'alias' => $alias_content,
                'idMain' => $book->getId()
            ]
        );
        if (!$soder) {
            $title = "Книга ".$book->getName().". Содержание:";
            $this->seo(
                $book->getName().". Содержание ",
                $book->getName().". Содержание",
                $title,
                $title
            );
            $vm = new ViewModel(['title' => $title]);
            $vm->setTemplate('application/index/zread');
            return $vm;
        }
        $page_str = $soder->getNum();
        $title = "Книга ".$book->getName().". Содержание - ".$soder->getName();
        $this->seo(
            $book->getName().". Содержание - ".$soder->getName(),
            $book->getName().". Содержание - ".$soder->getName(),
            $title,
            $title
        );
        /** @var  $repository \Application\Repository\TextRepository */
        $repository = $em->getRepository(Text::class);
        $query = $repository->getTexts($book->getId());
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(1);
        $paginator->setCurrentPageNumber($page_str);
        $paginator->setPageRange(6);
        $vm = new ViewModel(
            [
                'book'  => $book,
                'paginator'  => $paginator,
                'title' => $title,
                'route' => 'home/genre/one/book/content',
                'params' => $this->params()->fromRoute()
            ]
        );
        $vm->setTemplate('application/index/read_content');
        return $vm;
    }

    /**
     * @return Response|ViewModel
     */
    public function areadAction()
    {
        $em = $this->getEntityManager();
        $alias_book = strtolower($this->params()->fromRoute('book'));
        $page_str = $this->params()->fromRoute('page_str', 0);
        $alias_menu = strtolower($this->params()->fromRoute('alias_menu'));
        /** @var \Application\Entity\Book $book */
        $book = $em->getRepository(Book::class)->findOneByRep(['alias' => $alias_book, 'vis' => 1]);
        if (!$book or !$this->getAvtor($book)) {
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }

        $sm = $this->sm;
        if ($book->getBan() == 1  and $sm->get('User')->getRole() != 'admin') {
            return $this->redirect()->toUrl('/blocked-book/'.$book->getAlias().'/')
                ->setStatusCode(301);
        }

        /** @var \Application\Entity\MAvtor $avtor */
        $avtor = $em->getRepository(MAvtor::class)->findOneBy(['alias' => $alias_menu]);
        if(!$avtor){
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }
        if (!$page_str) {
            $title = "Книга ".$book->getName().". Автор: ".$avtor->getName().". Страницы:";
            $this->seo(
                $book->getName().". Автор: ".$avtor->getName().". Страницы",
                $book->getName().". Автор: ".$avtor->getName().". Страницы",
                $title,
                $title
            );
            $vm = new ViewModel(['title' => $title]);
            $vm->setTemplate('application/index/zread');
            return $vm;
        }
        $title = "Книга ".$book->getName().". Автор: ".$avtor->getName().". Страница "
            .$page_str;
        $this->seo(
            $book->getName().". Автор ".$avtor->getName().". Страница ".$page_str,
            $book->getName().". Автор ".$avtor->getName().". Страница ".$page_str,
            $title,
            $title
        );
        /** @var  $repository \Application\Repository\TextRepository */
        $repository = $em->getRepository(Text::class);
        $query = $repository->getTexts($book->getId());
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(1);
        $paginator->setCurrentPageNumber($page_str);
        $paginator->setPageRange(6);
        $vm = new ViewModel(
            [
                'book'  => $book,
                'paginator'  => $paginator,
                'title' => $title,
                'params' => $this->params()->fromRoute(),
                'route' => 'home/authors/one/book/read',
            ]
        );
        $vm->setTemplate('application/index/read_content');
        return $vm;
    }

    /**
     * @return ViewModel
     * @throws \Doctrine\ORM\ORMException
     */
    public function acontentAction()
    {
        $em = $this->getEntityManager();
        $alias_book = strtolower($this->params()->fromRoute('book'));
        $alias_content = strtolower($this->params()->fromRoute('content'));
        $alias_menu = strtolower($this->params()->fromRoute('alias_menu'));
        /** @var \Application\Entity\Book $book */
        $book = $em->getRepository(Book::class)->findOneByRep(['alias' => $alias_book, 'vis' => 1]);
        if (!$book or !$this->getAvtor($book)) {
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }
        $sm = $this->sm;
        if ($book->getBan() == 1  and $sm->get('User')->getRole() != 'admin') {
            return $this->redirect()->toUrl('/blocked-book/'.$book->getAlias().'/')
                ->setStatusCode(301);
        }
        /** @var \Application\Entity\MAvtor $avtor */
        $avtor = $em->getRepository(MAvtor::class)->findOneBy(['alias' => $alias_menu]);
        if(!$avtor){
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }
        /** @var \Application\Entity\Soder $soder */
        $soder = $em->getRepository(Soder::class)->findOneBy(
            [
                'alias' => $alias_content,
                'idMain' => $book->getId()
            ]
        );
        if (!$soder) {
            $title = "Книга ".$book->getName().". Автор - ".$avtor->getName().". Содержание:";
            $this->seo(
                $book->getName().". Автор - ".$avtor->getName().". Содержание",
                $book->getName().". Автор - ".$avtor->getName().". Содержание",
                $title,
                $title
            );
            $vm = new ViewModel(['title' => $title]);
            $vm->setTemplate('application/index/zread');
            return $vm;
        }
        $page_str = $soder->getNum();
        $title = "Книга ".$book->getName().". Автор - ".$avtor->getName().". Содержание - "
            .$soder->getName();
        $this->seo(
            $book->getName().". Автор - ".$avtor->getName().". Содержание - "
            .$soder->getName(),
            $book->getName().". Автор - ".$avtor->getName().". Содержание - "
            .$soder->getName(),
            $title,
            $title
        );
        /** @var  $repository \Application\Repository\TextRepository */
        $repository = $em->getRepository(Text::class);
        $query = $repository->getTexts($book->getId());
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(1);
        $paginator->setCurrentPageNumber($page_str);
        $paginator->setPageRange(6);
        $vm = new ViewModel(
            [
                'book'  => $book,
                'paginator'  => $paginator,
                'title' => $title,
                'params' => $this->params()->fromRoute(),
                'route' => 'home/authors/one/book/content',
            ]
        );
        $vm->setTemplate('application/index/read_content');
        return $vm;
    }

    /**
     * @return ViewModel
     * @throws \Doctrine\ORM\ORMException
     */
    public function sreadAction()
    {
        $em = $this->getEntityManager();
        $alias_book = strtolower($this->params()->fromRoute('book'));
        $page_str = $this->params()->fromRoute('page_str', 0);
        $alias_menu = strtolower($this->params()->fromRoute('alias_menu'));
        /** @var \Application\Entity\Book $book */
        $book = $em->getRepository(Book::class)->findOneByRep(['alias' => $alias_book, 'vis' => 1]);

        if (!$book  or !$this->getSerii($book)) {
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }
        $sm = $this->sm;
        if ($book->getBan() == 1  and $sm->get('User')->getRole() != 'admin') {
            return $this->redirect()->toUrl('/blocked-book/'.$book->getAlias().'/')
                ->setStatusCode(301);
        }
        /** @var \Application\Entity\MSerii $serii */
        $serii = $em->getRepository(MSerii::class)->findOneBy(['alias' => $alias_menu]);
        if(!$serii){
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }
        if (!$page_str) {
            $title = "Книга ".$book->getName().". Серия: ".$serii->getName().". Страницы:";
            $this->seo(
                $book->getName().". Серия: ".$serii->getName().". Страницы",
                $book->getName().". Серия: ".$serii->getName().". Страницы",
                $title,
                $title
            );
            $vm = new ViewModel(['title' => $title]);
            $vm->setTemplate('application/index/zread');
            return $vm;
        }
        $title = "Книга ".$book->getName().". Серия: ".$serii->getName().". Страница "
            .$page_str;
        $this->seo(
            $book->getName().". Серия ".$serii->getName().". Страница ".$page_str,
            $book->getName().". Серия ".$serii->getName().". Страница ".$page_str,
            $title,
            $title
        );
        /** @var  $repository \Application\Repository\TextRepository */
        $repository = $em->getRepository(Text::class);
        $query = $repository->getTexts($book->getId());
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(1);
        $paginator->setCurrentPageNumber($page_str);
        $paginator->setPageRange(6);
        $vm = new ViewModel(
            [
                'book'  => $book,
                'paginator'  => $paginator,
                'title' => $title,
                'params' => $this->params()->fromRoute(),
                'route' => 'home/series/one/book/read',
            ]
        );
        $vm->setTemplate('application/index/read_content');
        return $vm;
    }

    /**
     * @return ViewModel
     * @throws \Doctrine\ORM\ORMException
     */
    public function scontentAction()
    {
        $em = $this->getEntityManager();
        $alias_book = strtolower($this->params()->fromRoute('book'));
        $alias_content = strtolower($this->params()->fromRoute('content'));
        $alias_menu = strtolower($this->params()->fromRoute('alias_menu'));
        /** @var \Application\Entity\Book $book */
        $book = $em->getRepository(Book::class)->findOneByRep(['alias' => $alias_book, 'vis' => 1]);
        if (!$book or !$this->getSerii($book)) {
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }
        $sm = $this->sm;
        if ($book->getBan() == 1  and $sm->get('User')->getRole() != 'admin') {
            return $this->redirect()->toUrl('/blocked-book/'.$book->getAlias().'/')
                ->setStatusCode(301);
        }
        /** @var \Application\Entity\MSerii $serii */
        $serii = $em->getRepository(MSerii::class)->findOneBy(['alias' => $alias_menu]);
        if(!$serii){
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }
        /** @var \Application\Entity\Soder $soder */
        $soder = $em->getRepository(Soder::class)->findOneBy(
            [
                'alias' => $alias_content,
                'idMain' => $book->getId()
            ]
        );
        if (!$soder) {
            $title = "Книга ".$book->getName().". Серия - ".$serii->getName().". Содержание:";
            $this->seo(
                $book->getName().". Серия - ".$serii->getName().". Содержание",
                $book->getName().". Серия - ".$serii->getName().". Содержание",
                $title,
                $title
            );
            $vm = new ViewModel(['title' => $title]);
            $vm->setTemplate('application/index/zread');
            return $vm;
        }
        $page_str = $soder->getNum();
        $title = "Книга ".$book->getName().". Серия - ".$serii->getName().". Содержание - "
            .$soder->getName();
        $this->seo(
            $book->getName().". Серия - ".$serii->getName().". Содержание - "
            .$soder->getName(),
            $book->getName().". Серия - ".$serii->getName().". Содержание - "
            .$soder->getName(),
            $title,
            $title
        );
        /** @var  $repository \Application\Repository\TextRepository */
        $repository = $em->getRepository(Text::class);
        $query = $repository->getTexts($book->getId());
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(1);
        $paginator->setCurrentPageNumber($page_str);
        $paginator->setPageRange(6);
        $vm = new ViewModel(
            [
                'book'  => $book,
                'paginator'  => $paginator,
                'title' => $title,
                'params' => $this->params()->fromRoute(),
                'route' => 'home/series/one/book/read',
            ]
        );
        $vm->setTemplate('application/index/read_content');
        return $vm;
    }

    /**
     * @return ViewModel
     * @throws \Doctrine\ORM\ORMException
     */
    public function treadAction()
    {
        $em = $this->getEntityManager();
        $alias_book = strtolower($this->params()->fromRoute('book'));
        $page_str = $this->params()->fromRoute('page_str', 0);
        $alias_menu = strtolower($this->params()->fromRoute('alias_menu'));
        /** @var \Application\Entity\Book $book */
        $book = $em->getRepository(Book::class)->findOneByRep(['alias' => $alias_book, 'vis' => 1]);

        if (!$book or !$this->getTranslit($book)) {
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }
        $sm = $this->sm;
        if ($book->getBan() == 1  and $sm->get('User')->getRole() != 'admin') {
            return $this->redirect()->toUrl('/blocked-book/'.$book->getAlias().'/')
                ->setStatusCode(301);
        }
        /** @var \Application\Entity\MSerii $translit */
        $translit = $em->getRepository(MTranslit::class)->findOneBy(['alias' => $alias_menu]);
        if(!$translit){
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }
        if (!$page_str) {
            $title = "Книга ".$book->getName().". Переводчик: ".$translit->getName().". Страницы:";
            $this->seo(
                $book->getName().". Переводчик: ".$translit->getName().". Страницы",
                $book->getName().". Переводчик: ".$translit->getName().". Страницы",
                $title,
                $title
            );
            $vm = new ViewModel(['title' => $title]);
            $vm->setTemplate('application/index/zread');
            return $vm;
        }
        $title = "Книга ".$book->getName().". Переводчик: ".$translit->getName().". Страница "
            .$page_str;
        $this->seo(
            $book->getName().". Переводчик ".$translit->getName().". Страница ".$page_str,
            $book->getName().". Переводчик ".$translit->getName().". Страница ".$page_str,
            $title,
            $title
        );
        /** @var  $repository \Application\Repository\TextRepository */
        $repository = $em->getRepository(Text::class);
        $query = $repository->getTexts($book->getId());
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(1);
        $paginator->setCurrentPageNumber($page_str);
        $paginator->setPageRange(6);
        $vm = new ViewModel(
            [
                'book'  => $book,
                'paginator'  => $paginator,
                'title' => $title,
                'params' => $this->params()->fromRoute(),
                'route' => 'home/translit/one/book/read',
            ]
        );
        $vm->setTemplate('application/index/read_content');
        return $vm;
    }

    /**
     * @return ViewModel
     * @throws \Doctrine\ORM\ORMException
     */
    public function tcontentAction()
    {
        $em = $this->getEntityManager();
        $alias_book = strtolower($this->params()->fromRoute('book'));
        $alias_content = strtolower($this->params()->fromRoute('content'));
        $alias_menu = strtolower($this->params()->fromRoute('alias_menu'));
        /** @var \Application\Entity\Book $book */
        $book = $em->getRepository(Book::class)->findOneByRep(['alias' => $alias_book, 'vis' => 1]);
        if (!$book or !$this->getTranslit($book)) {
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }
        $sm = $this->sm;
        if ($book->getBan() == 1  and $sm->get('User')->getRole() != 'admin') {
            return $this->redirect()->toUrl('/blocked-book/'.$book->getAlias().'/')
                ->setStatusCode(301);
        }
        /** @var \Application\Entity\MTranslit $translit */
        $translit = $em->getRepository(MTranslit::class)->findOneBy(['alias' => $alias_menu]);
        if(!$translit){
            /** @var \Zend\Http\Response $response */
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }
        /** @var \Application\Entity\Soder $soder */
        $soder = $em->getRepository(Soder::class)->findOneBy(
            [
                'alias' => $alias_content,
                'idMain' => $book->getId()
            ]
        );

        if (!$soder) {
            $title = "Книга ".$book->getName().". Переводчик - ".$translit->getName().". Содержание:";
            $this->seo(
                $book->getName().". Переводчик - ".$translit->getName().". Содержание",
                $book->getName().". Переводчик - ".$translit->getName().". Содержание",
                $title,
                $title
            );
            $vm = new ViewModel(['title' => $title]);
            $vm->setTemplate('application/index/zread');
            return $vm;
        }
        $page_str = $soder->getNum();
        $title = "Книга ".$book->getName().". Переводчик - ".$translit->getName().". Содержание - "
            .$soder->getName();
        $this->seo(
            $book->getName().". Переводчик - ".$translit->getName().". Содержание - "
            .$soder->getName(),
            $book->getName().". Переводчик - ".$translit->getName().". Содержание - "
            .$soder->getName(),
            $title,
            $title
        );
        /** @var  $repository \Application\Repository\TextRepository */
        $repository = $em->getRepository(Text::class);
        $query = $repository->getTexts($book->getId());
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(1);
        $paginator->setCurrentPageNumber($page_str);
        $paginator->setPageRange(6);
        $vm = new ViewModel(
            [
                'book'  => $book,
                'paginator'  => $paginator,
                'title' => $title,
                'params' => $this->params()->fromRoute(),
                'route' => 'home/translit/one/book/read',
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
     * @return JsonModel
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function starsAction()
    {
        $em = $this->getEntityManager();
        /** @var $stars */
        $stars = $this->params()->fromQuery('stars');
        $id_book = $this->params()->fromQuery('id_book');
        $ip = $this->getIp();
        $err = 0;
        /** @var \Application\Entity\Stars $starsEntity */
        $starsEntity = $em->getRepository(Stars::class)->findOneBy(
            [
                'idBook' => $id_book,
                'ip'     => $ip,
            ]
        );
        /** @var \Application\Entity\Book $book */
        $book = $em->getRepository(Book::class)->find($id_book);
        if(!$starsEntity){
            $starsEntity = new Stars();
            $starsEntity->setStars($stars);
            $starsEntity->setIp($ip);
            $starsEntity->setIdBook($book);
            $starsEntity->setDatetimeCreated(new \Datetime());
        }
        else{
            $starsEntity->setStars($stars);
        }
        $em->persist($starsEntity);
        $em->flush();
        $stars = $em->getRepository(Stars::class)->findBy(
            [
                'idBook' => $id_book,
            ]
        );
        $num_stars = 0;
        $count = 0;
        foreach ($stars as $star) {
            /** @var \Application\Entity\Stars $star */
            $count++;
            $num_stars += $star->getStars();

        }
        $aver_value = (float)($num_stars / $count);
        $book->setStars($aver_value);
        $book->setCountStars($count);
        $em->persist($book);
        $em->flush();
        return new JsonModel(
            [
                'stars' => $aver_value,
                'count' => $count,
                'err'   => $err,
            ]
        );
    }

}
