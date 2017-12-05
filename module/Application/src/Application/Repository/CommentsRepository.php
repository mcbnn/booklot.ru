<?php

namespace Application\Repository;

use Application\Entity\Comments;
use Doctrine\ORM\EntityRepository;

class CommentsRepository extends EntityRepository
{
    /**
     * @param null $user_id
     *
     * @return array|\Doctrine\ORM\Query
     */
    public function getCommentsUser($user_id = null){
        if($user_id == null) return [];

        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('c')
            ->from(Comments::class, 'c')
            ->where('c.idUser = :idUser')
            ->orderBy('c.id', 'DESC')
            ->setParameter('idUser', $user_id);

        return $queryBuilder->getQuery();
    }
}
