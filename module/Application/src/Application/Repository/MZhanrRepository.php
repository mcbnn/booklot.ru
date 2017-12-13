<?php

namespace Application\Repository;

use Application\Entity\MZhanr;
use Doctrine\ORM\EntityRepository;

class MZhanrRepository extends EntityRepository
{
    /**
     * @param array $get
     *
     * @return array
     */
    public function getChild($get = []){

        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('c')
            ->from(MZhanr::class, 'c')
            ->where('c.route = :route')
            ->andWhere('c.see = :see')
            ->andWhere('c.vis = :vis')
            ->andWhere('c.idMain != :idMain')
            ->andWhere('c.countBook > :countBook')
            ->orderBy('c.name', 'ASC')
            ->setParameters(
                [
                    'route' => 'home/genre/one',
                    'see'   => 1,
                    'vis' => 1,
                    'idMain' => 500,
                    'countBook' => 10
                ]
            );
            if(isset($get['name_zhanr'])){
                $queryBuilder->andWhere('c.name LIKE :name_zhanr');
                $queryBuilder->setParameter(  'name_zhanr', '%'.$get['name_zhanr'].'%');
            }
        return $queryBuilder->getQuery()->getResult();
    }
}
