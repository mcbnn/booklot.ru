<?php

namespace Application\Repository;

use Application\Entity\MyBook;
use Doctrine\ORM\EntityRepository;

class MyBookRepository extends EntityRepository
{
    /**
     * @param null $user_id
     *
     * @return array|\Doctrine\ORM\Query
     */
    public function getMyBookUser($user_id = null){
        if($user_id == null) return [];

        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('c')
            ->from(MyBook::class, 'c')
            ->where('c.user = :user')
            ->orderBy('c.id', 'DESC')
            ->setParameter('user', $user_id);

        return $queryBuilder->getQuery();
    }
}
