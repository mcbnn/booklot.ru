<?php

namespace Application\Repository;

use Application\Entity\MAvtor;
use Doctrine\ORM\EntityRepository;

class MAvtorRepository extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\Query
     */
    public function getAvtors()
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('ma')
            ->from(MAvtor::class, 'ma')
            ->orderBy('ma.name', 'ASC');
        return $queryBuilder->getQuery();
    }
    /**
     * @param null $name
     *
     * @return array
     */
    public function getAvtorsName($name = null)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $name = htmlspecialchars(mb_strtolower("%$name%", 'UTF-8'));
        $queryBuilder->select('ma')
            ->from(MAvtor::class, 'ma')
            ->where('LOWER(ma.name) LIKE :name')
            ->orderBy('ma.name', 'ASC')
            ->setParameter('name', $name)
            ->setMaxResults(10);
        return $queryBuilder->getQuery()->getResult();
    }
}
