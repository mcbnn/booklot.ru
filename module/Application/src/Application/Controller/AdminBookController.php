<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 05.12.17
 * Time: 13:32
 */

namespace Application\Controller;

use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Form\BookForm;
use Application\Entity\Book;
use Application\Entity\MZhanr;
use Application\Entity\Zhanr;
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
     * @return array|\Doctrine\ORM\EntityManager|object
     */
    protected function getEntityManager()
    {
        if ($this->em == null) {
            $this->em = $this->getServiceLocator()->get(
                'doctrine.entitymanager.orm_default'
            );
        }

        return $this->em;
    }


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
            $this->getServiceLocator()->get('arraySort'),
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
     */
    protected function addAction($type = 'add')
    {
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
        $sm = $this->getServiceLocator();
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

                    $hash = md5(time()).$adapter->getHash();
                    $nameFile = $hash.$filename;
                    $adapter->addFilter(
                        'File\Rename',
                        [
                            'target' => 'public/templates/newimg/original/'
                                .$nameFile,
                            'overwrite' => true,
                        ]
                    );
                    if (!$adapter->receive()) {
                        echo implode("", $adapter->getMessages());
                    }

                    $dir = '/var/www/booklot2.ru/www/templates/newimg/';
                    $sm->get('Main')->foto_loc1(
                        $dir.'original/'.$nameFile,
                        '170',
                        $dir.'small/',
                        $nameFile
                    );
                    $sm->get('Main')->foto_loc1(
                        $dir.'original/'.$nameFile,
                        '300',
                        $dir.'full/',
                        $nameFile
                    );
                    $book->setFoto($nameFile);
                }

                $alias = $sm->get('Main')->trans($request->getPost('name'));
                if ($type == 'add') {
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
                }
                $book->setDateAdd(new \Datetime());
                /** @var $menu \Application\Entity\MZhanr */
                $menu = $em->getRepository(MZhanr::class)->find(
                    $request->getPost('menu')
                );

                $book->setMenu($menu);
                $book->setAlias($alias);
                $book->setNS($menu->getParent()->getAlias());
                $book->setNAliasMenu($menu->getAlias());
                $book->setNameZhanr($menu->getName());
                $em->persist($book);
                $em->flush();

                return $this->redirect()->toRoute(
                    'home/admin-book',
                    ['action' => 'edit', 'id' => $book->getId()]
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
     */
    public function editAction()
    {
        return $this->addAction('edit');
    }

    public function deleteAction(){
        $id = $this->params()->fromRoute('id', null);
        $em = $this->getEntityManager();
        /** @var $book \Application\Entity\Book */
        $book = $em->getRepository(Book::class)->find($id);
        $zhanr =  $em->getRepository(Zhanr::class)->findBy(
            [
                'idMain' => $book->getId()
            ]
        );
        if(count($zhanr)){
            foreach($zhanr as $value){
                $em->remove($value);
            }
        }
        $avtor =  $em->getRepository(Avtor::class)->findBy(
            [
                'idMain' => $book->getId()
            ]
        );
        if(count($avtor)){
            foreach($avtor as $value){
                $em->remove($value);
            }
        }
        $translit =  $em->getRepository(Translit::class)->findBy(
            [
                'idMain' => $book->getId()
            ]
        );
        if(count($avtor)){
            foreach($translit as $value){
                $em->remove($value);
            }
        }
        $serii =  $em->getRepository(Serii::class)->findBy(
            [
                'idMain' => $book->getId()
            ]
        );
        if(count($serii)){
            foreach($serii as $value){
                $em->remove($value);
            }
        }
        $text =  $em->getRepository(Text::class)->findBy(
            [
                'idMain' => $book->getId()
            ]
        );
        if(count($text)){
            foreach($text as $value){
                $em->remove($value);
            }
        }
        $stars =  $em->getRepository(Stars::class)->findBy(
            [
                'idBook' => $book->getId()
            ]
        );
        if(count($stars)){
            foreach($stars as $value){
                $em->remove($value);
            }
        }
        $soder =  $em->getRepository(Soder::class)->findBy(
            ['idMain' => $book->getId()
            ]
        );
        if(count($soder)){
            foreach($soder as $value){
                $em->remove($value);
            }
        }
        $myBookStatus =  $em->getRepository(MyBookStatus::class)->findBy(
            [
                'book' => $book->getId()
            ]
        );
        if(count($myBookStatus)){
            foreach($myBookStatus as $value){
                $em->remove($value);
            }
        }
        $myBookLike =  $em->getRepository(MyBookLike::class)->findBy(
            [
                'book' => $book->getId()
            ]
        );
        if(count($myBookLike)){
            foreach($myBookLike as $value){
                $em->remove($value);
            }
        }
        $myBook =  $em->getRepository(MyBook::class)->findBy(
            [
                'book' => $book->getId()
            ]
        );
        if(count($myBook)){
            foreach($myBook as $value){
                $em->remove($value);
            }
        }
        $commentsFaik =  $em->getRepository(CommentsFaik::class)->findBy(
            [
                'book' => $book->getId()
            ]
        );
        if(count($commentsFaik)){
            foreach($commentsFaik as $value){
                $em->remove($value);
            }
        }
        $comments =  $em->getRepository(Comments::class)->findBy(
            [
                'book' => $book->getId()
            ]
        );
        if(count($comments)){
            foreach($comments as $value){
                $em->remove($value);
            }
        }
        $em->flush();
        $bookFiles =  $em->getRepository(BookFiles::class)->findBy(
            [
                'idBook' => $book->getId()
            ]
        );
        if(count($bookFiles)){
            foreach($bookFiles as $value){
                $em->remove($value);
            }
        }
        $em->remove($book);
        $em->flush();
        die();
        return $this->redirect()->toRoute('home/admin-book');
    }

}