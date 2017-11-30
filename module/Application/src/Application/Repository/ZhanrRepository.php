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
        IDENTITY(z.idMenu) as id_menu,
        count(1) AS c 
        FROM  Application\Entity\Zhanr  z
        GROUP BY z.idMain, z.idMenu
        HAVING COUNT(1) > 1
        "
        )->getResult();

        return $result;
    }
}
