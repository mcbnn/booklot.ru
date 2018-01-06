<?php

namespace Application\Repository;

use Application\Entity\MTranslit;
use Doctrine\ORM\EntityRepository;

class MTranslitRepository extends EntityRepository
{
    public function getTranslits(){
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('mt')
            ->from(MTranslit::class, 'mt')
            ->orderBy('mt.name', 'ASC');
        return $queryBuilder->getQuery();
    }
    /**
     * @param null $name
     *
     * @return array
     */
    public function getTranslitName($name = null)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $name = htmlspecialchars(mb_strtolower("%$name%", 'UTF-8'));
        $queryBuilder->select('mt')
            ->from(MTranslit::class, 'mt')
            ->where('LOWER(mt.name) LIKE :name')
            ->orderBy('mt.name', 'ASC')
            ->setParameter('name', $name)
            ->setMaxResults(10);
        return $queryBuilder->getQuery()->getResult();
    }
}
