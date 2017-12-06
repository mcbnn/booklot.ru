<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 05.12.17
 * Time: 13:32
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Application\Entity\MyBook;
use Application\Entity\MyBookStatus;
use Application\Entity\MyBookStatusName;
use Application\Entity\MyBookLike;
use Application\Entity\Book;
use Application\Entity\Bogi;
use Zend\View\Model\ViewModel;

class EventsController extends AbstractActionController
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

    public function addBookLikeAction(){
        $book_id = $this->params()->fromPost('book_id', false);
        if (!$book_id) {
            return new JsonModel(
                [
                    'text'  => 'Нет такой книги',
                    'error' => 1,
                ]
            );
        }
        $user = $this->getServiceLocator()->get('AuthService')->getIdentity();
        if ($user == null) {
            return new JsonModel(
                [
                    'text'  => 'Вы не авторизованы',
                    'error' => 1,
                ]
            );
        }

        $em = $this->getEntityManager();
        $repository = $em->getRepository(MyBookLike::class);

        $findOneBy = $repository->findOneBy(
            [
                'book' => $book_id,
                'user' => $user->id,
            ]
        );

        $book_ = $em->getRepository(Book::class)->find($book_id);
        $user_ = $em->getRepository(Bogi::class)->find($user->id);
        if($findOneBy == null){

            $myBookLike = new MyBookLike();
            $myBookLike->setBook($book_);
            $myBookLike->setUser($user_);
            $em->persist($myBookLike);
            $em->flush();

        }
        else{

            $em->remove($findOneBy);
            $em->flush();

        }

        $findBy = $repository->findBy(
            [
                'book' => $book_id,
            ]
        );
        return new JsonModel(
            [
                'text'  => count($findBy),
                'error' => 0,
            ]
        );
    }

    /**
     * @return JsonModel
     */
    public function addStatusBookAction()
    {

        $book_id = $this->params()->fromPost('book_id', false);
        if (!$book_id) {
            return new JsonModel(
                [
                    'text'  => 'Нет такой книги',
                    'error' => 1,
                ]
            );
        }

        $status_id = $this->params()->fromPost('status_id', false);

        $user = $this->getServiceLocator()->get('AuthService')->getIdentity();
        if ($user == null) {
            return new JsonModel(
                [
                    'text'  => 'Вы не авторизованы',
                    'error' => 1,
                ]
            );
        }

        $em = $this->getEntityManager();
        $repository = $em->getRepository(MyBookStatus::class);

        $findOneBy = $repository->findOneBy(
            [
                'book' => $book_id,
                'user' => $user->id,
            ]
        );

        if($status_id){
            $status_ =  $em->getRepository(MyBookStatusName::class)->find($status_id);
        }
        $book_ = $em->getRepository(Book::class)->find($book_id);
        $user_ = $em->getRepository(Bogi::class)->find($user->id);
        if($findOneBy == null){
            if($status_id){

                $myBookStatus = new MyBookStatus();
                $myBookStatus->setBook($book_);
                $myBookStatus->setUser($user_);
                $myBookStatus->setStatus($status_);
                $em->persist($myBookStatus);
                $em->flush();

            }
        }
        else{
            if($status_id){

                $findOneBy->setStatus($status_);
                $em->flush($findOneBy);

            }
            else{

                $em->remove($findOneBy);
                $em->flush();

            }
        }
        return new JsonModel(
            [
                'text'  => 'Статус выбран',
                'error' => 0,
            ]
        );

    }

    /**
     * @return JsonModel
     */
    public function addMyBookAction()
    {

        $book_id = $this->params()->fromPost('book_id', false);
        if (!$book_id) {
            return new JsonModel(
                [
                    'text'  => 'Нет такой книги',
                    'error' => 1,
                ]
            );
        }

        $user = $this->getServiceLocator()->get('AuthService')->getIdentity();
        if ($user == null) {
            return new JsonModel(
                [
                    'text'  => 'Вы не авторизованы',
                    'error' => 1,
                ]
            );
        }

        $em = $this->getEntityManager();
        $repository = $em->getRepository(MyBook::class);

        $findOneBy = $repository->findOneBy(
            [
                'book' => $book_id,
                'user' => $user->id,
            ]
        );


        if ($findOneBy == null) {
            $myBook = new MyBook();
            $myBook->setBook($em->getRepository(Book::class)->find($book_id));
            $myBook->setUser($em->getRepository(Bogi::class)->find($user->id));
            $em->persist($myBook);
            $em->flush();
            $my_book = true;
        } else {
            $em->remove($findOneBy);
            $em->flush();
            $my_book = false;
        }

        $findBy = $repository->findBy(
            [
                'user' => $user->id
            ]
        );

        $bogi = $em->getRepository(Bogi::class);
        $bogi = $bogi->find($user->id);
        $bogi->setMyBook(count($findBy));
        $em->flush($bogi);
        $vm = new ViewModel(
            [
                'id'      => $book_id,
                'my_book' => $my_book,
            ]
        );
        $vm->setTemplate('application/button/my-book');
        $vm->setTerminal(true);
        $viewRender = $this->getServiceLocator()->get('ViewRenderer');
        $html = $viewRender->render($vm);

        return new JsonModel(
            [
                'error' => 0,
                'text'  => $html,
            ]
        );
    }

}