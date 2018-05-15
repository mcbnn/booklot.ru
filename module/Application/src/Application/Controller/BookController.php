<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 05.12.17
 * Time: 13:32
 */

namespace Application\Controller;

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity\Book;
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
use Application\Entity\BookNotes;

class BookController extends AbstractActionController
{
    /**
     * @var null|ServiceManager
     */
    public $sm = null;

    public $em = null;

    public function __construct(ServiceManager $servicemanager)
    {
        /** @var \Zend\ServiceManager\ServiceManager sm */
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

    public function deleteBook($id = null){
        if(!$id)return [];
        $em = $this->getEntityManager();
        /** @var $book \Application\Entity\Book */
        $book = $em->getRepository(Book::class)->find($id);
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
        $fileParse =  $em->getRepository(FilesParse::class)->findBy(
            [
                'bookId' => $book->getId()
            ]
        );
        if(count($fileParse)){
            foreach($fileParse as $value){
                $em->remove($value);
            }
        }
        $bookNotes =  $em->getRepository(BookNotes::class)->findBy(
            [
                'book' => $book->getId()
            ]
        );
        if(count($bookNotes)){
            foreach($bookNotes as $value){
                $em->remove($value);
            }
        }
        $em->remove($book);
        $em->flush();
    }

}