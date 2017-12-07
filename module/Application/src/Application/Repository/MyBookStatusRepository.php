<?php

namespace Application\Repository;

use Application\Entity\MyBookStatus;
use Application\Entity\Book;
use Doctrine\ORM\EntityRepository;

class MyBookStatusRepository extends EntityRepository
{
    /**
     * @param null $user_id
     *
     * @return array|\Doctrine\ORM\Query
     */
    public function getMyBookStatusUser($user_id = null, $get = []){
        if($user_id == null) return [];

        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('c')
            ->from(MyBookStatus::class, 'c')
            ->where('c.user = :user')
            ->orderBy('c.id', 'DESC')
            ->setParameter('user', $user_id);

        if(isset($get['book_name'])){
            $queryBuilder->innerJoin(Book::class,'b', 'WITH', 'b.id = c.book');
            $queryBuilder->andWhere('b.name LIKE :book_name');
            $queryBuilder->setParameter(  'book_name', '%'.$get['book_name'].'%');
        }

        if(isset($get['status_id']) and $get['status_id'] != 0){
            $queryBuilder->andWhere('c.status = :status_id');
            $queryBuilder->setParameter(  'status_id', $get['status_id']);
        }
        return $queryBuilder->getQuery();
    }
}
