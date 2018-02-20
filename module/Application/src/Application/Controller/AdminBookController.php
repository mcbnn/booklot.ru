<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 05.12.17
 * Time: 13:32
 */

namespace Application\Controller;

use Zend\ServiceManager\ServiceManager;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Form\BookForm;
use Application\Entity\Book;
use Application\Entity\MZhanr;
use Application\Entity\Avtor;
use Application\Entity\Serii;
use Application\Entity\Translit;
use Application\Entity\Text;
use Application\Entity\Stars;
use Application\Entity\Soder;
use Application\Entity\MyBookStatus;
use Application\Entity\MyBookLike;
use Application\Entity\MyBook;
use Application\Entity\CommentsFaik;
use Application\Entity\Comments;
use Application\Entity\BookFiles;
use Application\Entity\FilesParse;
use Zend\View\Model\ViewModel;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\File\Transfer\Adapter\Http;
use Zend\Validator\File\Extension;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator as ZendPaginator;

class AdminBookController extends AbstractActionController
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
        $page = $this->params()->fromRoute('id', 1);
        $get = $this->params()->fromQuery();
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Repository\BookRepository */
        $repository = $em->getRepository(Book::class);
        $name = $this->params()->fromQuery('book_name_admin', null);
        $vis = $this->params()->fromQuery('vis_admin', null);
        $where = null;
        if($name){
            $where['where']['b_name']['type'] = "LIKE";
            $where['where']['b_name']['value'] = mb_strtolower("%$name%", 'utf-8');
            $where['where']['b_name']['column'] = 'LOWER(b.name)';
            $where['where']['b_name']['operator'] = 'and';
        }
        if($vis != null){
            $where['where']['b_vis']['type'] = "=";
            $where['where']['b_vis']['value'] = $vis;
            $where['where']['b_vis']['column'] = 'b.vis';
            $where['where']['b_vis']['operator'] = 'and';
        }
        $query = $repository->getBooksQuery(
            ['order' => ['b.dateAdd' => 'desc']],
            $where,
            false
        );
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(100);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(6);
        $vm = new ViewModel(
            [
                'paginator' => $paginator,
                'get'       => $get,
            ]
        );
        return $vm;
    }

    /**
     * @param string $type
     *
     * @return Response|ViewModel
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function addAction($type = 'add')
    {
        $config = $this->sm->get('config');
        $id = $this->params()->fromRoute('id', null);
        $em = $this->getEntityManager();
        $book = new Book();

        if ($id) {
            $book = $this->getEntityManager()->getRepository(
                Book::class
            )->find($id);
        }

        $form = new BookForm($this->getEntityManager());
        $form->setHydrator(
            new DoctrineObject (
                $this->getEntityManager(), 'Application\Entity\Book'
            )
        );

        $form->bind($book);
        /** @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $adapter = new Http();
                $adapter->setValidators(
                    [
                        new Extension(
                            [
                                'extension' => ['jpg', 'jpeg', 'png', 'gif'],
                            ]
                        ),
                    ]
                );

                $filename = $adapter->getFilename();
                if ($filename != null) {
                    $filename = basename($filename);
                    $fileInfo = $adapter->getFileInfo();
                    $hash = md5(time()).$adapter->getHash();
                    $name_file = $hash.$filename;
                    $file = $config['UPLOAD_DIR'].'newimg/original/'.$name_file;
                    var_dump($file);

                    if(!move_uploaded_file($fileInfo['foto']['tmp_name'], $file)){
                        echo 'Проблема с с загрузкой'; die();
                    };
                    copy($file, $config['UPLOAD_DIR'].'newimg/small/'.$name_file);
                    copy($file, $config['UPLOAD_DIR'].'newimg/full/'.$name_file);
                    $image['name'] = $name_file;

                    $book->setFoto($filename);
                    var_dump($book);
                    die();
                }
                if ($type == 'add') {
                    $alias = $this->sm->get('Main')->trans($request->getPost('name'));
                    do {
                        /** @var $findBy \Application\Entity\Book */
                        $findBy = $em->getRepository(Book::class)->findOneBy(
                                ['alias' => $alias]
                            );
                        $count = 0;
                        if ($findBy != 0) {
                            $alias = $alias.'-';
                            $count = 1;
                        };
                    } while ($count != 0);
                    $book->setAlias($alias);
                }
                $book->setDateAdd(new \Datetime());
                /** @var $menu \Application\Entity\MZhanr */
                $menu = $em->getRepository(MZhanr::class)->find(
                    $request->getPost('menu')
                );
                $book->setMenu($menu);
                $book->setNS($menu->getParent()->getAlias());
                $book->setNAliasMenu($menu->getAlias());
                $book->setNameZhanr($menu->getName());
                $em->persist($book);
                $em->flush();
                return $this->redirect()->toRoute(
                    'home/admin-book',
                    [
                        'action' => 'edit',
                        'id' => $book->getId()
                    ]
                );
            }
        }
        return new ViewModel(
            [
                'form' => $form,
                'book' => $book,
            ]
        );
    }

    /**
     * @return Response|ViewModel
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function editAction()
    {
        return $this->addAction('edit');
    }

    /**
     * @return Response
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id', null);
        /** @var  $bookFactory \Application\Controller\BookController */
        $bookFactory = $this->sm->get('book');
        $bookFactory->deleteBook($id);
        return $this->redirect()->toRoute('home/admin-book');
    }
}