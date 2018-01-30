<?php

namespace Application\Repository;

use Application\Entity\MAvtor;
use Doctrine\ORM\EntityRepository;

class MAvtorRepository extends EntityRepository
{
    public function findLikeAlias($alias){
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $alias = htmlspecialchars(mb_strtolower("%$alias%", 'UTF-8'));
        $queryBuilder->select('ma')
            ->from(MAvtor::class, 'ma')
            ->where('LOWER(ma.alias) LIKE :alias')
            ->orderBy('ma.alias', 'ASC')
            ->setParameter('alias', $alias);
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function checkAliasAuthors()
    {
        $result = $this->getEntityManager()->createQuery(
            "
                    select  ma from Application\Entity\MAvtor  ma
                    where  (
                    select count(1) from Application\Entity\MAvtor  ma1
                    where LOWER(ma1.alias) LIKE LOWER(ma.alias)
              
                    ) > 1
                    
                  
"
        )->getResult();

        return $result;
    }

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
