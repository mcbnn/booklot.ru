<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 05.12.17
 * Time: 13:32
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;
use Application\Entity\Comments;
use Application\Entity\Book;
use Application\Entity\Bogi;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator as ZendPaginator;

class CommentsController  extends AbstractActionController
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
     * @return JsonModel
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteAction(){
        $user = $this->sm->get('AuthService')->getIdentity();
        if ($user == null) {
            return new JsonModel(
                [
                    'text'  => 'Вы не авторизованы',
                    'error' => 1,
                ]
            );
        }
        $comment_id = $this->params()->fromPost('comment_id');
        if (!$comment_id) {
            return new JsonModel(
                [
                    'text'  => 'Нет такого комментария',
                    'error' => 1,
                ]
            );
        }
        $em = $this->getEntityManager();
        $repository = $em->getRepository(Comments::class);
        $findOneBy = $repository->findOneBy(
            [
                'id' => $comment_id,
                'user' => $user->id,
            ]
        );
        if($findOneBy == null){
            return new JsonModel(
                [
                    'text'  => 'Нет такого комментария',
                    'error' => 1,
                ]
            );
        }
        $em->remove($findOneBy);
        $em->flush();
        return new JsonModel(
            [
                'text'  => $comment_id,
                'error' => 0,
            ]
        );
    }

    /**
     * @return JsonModel
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addAction()
    {
        $user = $this->sm->get('AuthService')->getIdentity();
        if ($user == null) {
            return new JsonModel(
                [
                    'text'  => 'Вы не авторизованы',
                    'error' => 1,
                ]
            );
        }
        $book_id = $this->params()->fromPost('book_id');
        if (!$book_id) {
            return new JsonModel(
                [
                    'text'  => 'Нет такой книги',
                    'error' => 1,
                ]
            );
        }

        $text = $this->params()->fromPost('text');
        $text = strip_tags($text, '<div><img><br><b><ul><li>');
        if (strlen($text) < 1) {
            return new JsonModel(
                [
                    'text'  => 'Комментарий слишком маленький',
                    'error' => 1,
                ]
            );
        }
        $em = $this->getEntityManager();
        $comments = new Comments();
        $comments->setUser($em->getRepository(Bogi::class)->find($user->id));
        $comments->setBook($em->getRepository(Book::class)->find($book_id));
        $comments->setDatetime(new \DateTime("now"));
        $comments->setText($text);
        $em->persist($comments);
        $em->flush();
        $viewModel = new ViewModel(['comment' => $comments]);
        $viewModel->setTerminal(true);
        $viewModel->setTemplate('application/comments/one');
        $viewRender = $this->sm->get('ViewRenderer');
        $html = $viewRender->render($viewModel);
        return new JsonModel(
            [
                'text'  => $html,
                'error' => 0,
            ]
        );
    }

    /**
     * @return ViewModel
     */
    public function ListAction()
    {
        $user = $this->sm->get('AuthService')->getIdentity();
        $page = $this->params()->fromRoute('page', 1);
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Repository\MyBookRepository */
        $repository = $em->getRepository(MyBook::class);
        $query = $repository->getMyBookUser($user->id);
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page);
        $vm = new ViewModel(
            [
                'paginator' => $paginator
            ]
        );
        return $vm;
    }
}