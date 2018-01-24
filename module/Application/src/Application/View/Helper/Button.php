<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;
use Doctrine\ORM\EntityManager;
use Application\Entity\Bogi;
use Application\Entity\MyBook;
use Application\Entity\MyBookStatus;
use Application\Entity\MyBookStatusName;
use Application\Entity\MyBookLike;
use Application\Entity\Comments;

class Button extends AbstractHelper
{
    /**
     * @var null|AuthenticationService
     */
    public $as = null;

    public $em = null;
    /**
     * Button constructor.
     *
     * @param AuthenticationService $AuthService
     */
    public function __construct(AuthenticationService $AuthService, EntityManager $EntityManager)
    {
        /** @var \Zend\Authentication\AuthenticationService as */
        $this->as = $AuthService;
        /** @var \Doctrine\ORM\EntityManager em */
        $this->em = $EntityManager;
    }

    /**
     * @param null $link
     *
     * @return null
     */
    public function changeBook($link = null)
    {
        if(!$link) return null;
        if(!empty($this->as->hasIdentity()) and $this->as->getIdentity()->role == 'admin'){
            return $link;
        }
    }

    /**
     * @return null|string
     */
    public function userParams()
    {
        if($this->as->hasIdentity())return null;
        $id = $this->as->getIdentity()->id;
        $user = $this->em->getRepository(Bogi::class)->find($id);
        return $this->getView()->render('application/user/params',
            [
                'user' => $user,
            ]
        );
    }

    /**
     * @param null $book_id
     *
     * @return null|string
     */
    public function myBook($book_id = null)
    {
        if(!$book_id)return null;
        $repository = $this->em->getRepository(MyBook::class);
        $my_book = false;
        if($this->as->hasIdentity()) {
            $user_id =  $this->as->getIdentity()->id;;
            $findOneBy = $repository->findOneBy(
                [
                    'book' => $book_id,
                    'user' => $user_id
                ]
            );
            if($findOneBy){
                $my_book = true;
            }
        }
        return $this->getView()->render('application/button/my-book',
            [
                'id' => $book_id,
                'my_book' => $my_book
            ]
        );
    }

    /**
     * @param null $book_id
     *
     * @return null|string
     */
    public function myBookStatus($book_id = null)
    {
        if(!$book_id)return null;
        $status_all = $this->em->getRepository(MyBookStatusName::class)->findAll();
        $selected_status = null;
        if($this->as->hasIdentity()) {
            $user_id =  $this->as->getIdentity()->id;
            $selected_status = $this->em->getRepository(MyBookStatus::class)->findOneBy(
                    [
                        'book' => $book_id,
                        'user' => $user_id
                    ]
                );
        }
        return $this->getView()->render('application/button/my-book-status',
            [
                'id'         => $book_id,
                'status_all' => $status_all,
                'selected_status'   => $selected_status,
            ]
        );
    }

    /**
     * @param null $book_id
     *
     * @return null|string
     */
    public function myBookLike($book_id = null)
    {
        if(!$book_id)return null;
        $like = null;
        $repository = $this->em->getRepository(MyBookLike::class);
        if($this->as->hasIdentity()) {
            $user_id =  $this->as->getIdentity()->id;
            $like = $repository->findOneBy(
                [
                    'book' => $book_id,
                    'user' => $user_id
                ]
            );
        }
        $findAll = $repository->findOneBy(
            [
                'book' => $book_id
            ]
        );
        return $this->getView()->render('application/button/my-book-like',
            [
                'id'         => $book_id,
                'like'   => $like,
                'count_like' => count($findAll)
            ]
        );
    }

    /**
     * @param null $book_id
     * @return null
     */
    public function comments($book_id = null){
        if(!$book_id)return null;
        $repository = $this->em->getRepository(Comments::class);
        $comments = $repository->findBy(
            [
                'book' => $book_id
            ]
        );
        $user_id = 0;
        if($this->as->hasIdentity()) {
            $user_id = $this->as->getIdentity()->id;
        }
        return $this->getView()->render('application/comments/list',
            [
                'id'       => $book_id,
                'comments' => $comments,
                'user_id'  => $user_id,

            ]
        );
    }
}