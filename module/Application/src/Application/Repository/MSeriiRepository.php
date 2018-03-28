<?php

namespace Application\Repository;

use Application\Entity\MSerii;
use Doctrine\ORM\EntityRepository;

class MSeriiRepository extends EntityRepository
{

    public function getResultLike($name = null)
    {
        if(!$name)return [];
        $name1 = htmlspecialchars(mb_strtolower("$name #%", 'UTF-8'));
        $name2 = htmlspecialchars(mb_strtolower("$name", 'UTF-8'));

        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('ms')
            ->from(MSerii::class, 'ms')
            ->where('LOWER(ms.name) LIKE :name')
            ->orWhere('LOWER(ms.name) = :name1')
            ->setParameter('name', $name1)
            ->setParameter('name1', $name2)
            ->orderBy('ms.id', 'ASC');
        return $queryBuilder->getQuery()->getResult();
    }
    /**
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function getResults(){
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager
            ->createQuery("Select ma from Application\Entity\MSerii  ma");
        return $queryBuilder->iterate();
    }

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
