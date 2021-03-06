<?php

namespace Application\Repository;

use Application\Entity\MTranslit;
use Doctrine\ORM\EntityRepository;

class MTranslitRepository extends EntityRepository
{
    public function getDubleAlias()
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('b.alias')
            ->from(MTranslit::class, 'b')
            ->groupBy('b.alias')
            ->having('count(b.alias) > 1')
        ;
        return $queryBuilder->getQuery()->getResult();
    }
    /**
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function getResults(){
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager
            ->createQuery("Select ma from Application\Entity\MTranslit  ma");
        return $queryBuilder->iterate();
    }

    /**
     * @return \Doctrine\ORM\Query
     */
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
