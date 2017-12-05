<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

class ZhanrRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getDuble()
    {
        $result = $this->getEntityManager()->createQuery(
            "
        SELECT  
        IDENTITY(z.idMain) as id_main, 
        count(1) AS c 
        FROM  Application\Entity\Zhanr  z
        GROUP BY z.idMain
        HAVING COUNT(1) > 1
        "
        )->getResult();

        return $result;
    }
}
