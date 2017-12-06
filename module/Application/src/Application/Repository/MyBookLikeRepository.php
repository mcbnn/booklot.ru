<?php

namespace Application\Repository;

use Application\Entity\MyBookLike;
use Doctrine\ORM\EntityRepository;

class MyBookLikeRepository extends EntityRepository
{
    /**
     * @param null $user_id
     *
     * @return array|\Doctrine\ORM\Query
     */
    public function getMyBookLikeUser($user_id = null){
        if($user_id == null) return [];

        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('c')
            ->from(MyBookLike::class, 'c')
            ->where('c.user = :user')
            ->orderBy('c.id', 'DESC')
            ->setParameter('user', $user_id);

        return $queryBuilder->getQuery();
    }
}
