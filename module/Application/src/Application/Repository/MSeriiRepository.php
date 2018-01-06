<?php

namespace Application\Repository;

use Application\Entity\MSerii;
use Doctrine\ORM\EntityRepository;

class MSeriiRepository extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\Query
     */
    public function getSerii()
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('ms')
            ->from(MSerii::class, 'ms')
            ->orderBy('ms.name', 'ASC');
        return $queryBuilder->getQuery();
    }

    /**
     * @param null $name
     *
     * @return array
     */
    public function getSeriiName($name = null)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $name = htmlspecialchars(mb_strtolower("%$name%", 'UTF-8'));
        $queryBuilder->select('ms')
            ->from(MSerii::class, 'ms')
            ->where('LOWER(ms.name) LIKE :name')
            ->orderBy('ms.name', 'ASC')
            ->setParameter('name', $name)
            ->setMaxResults(10);
        return $queryBuilder->getQuery()->getResult();
    }
}
