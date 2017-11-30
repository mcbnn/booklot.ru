<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

class BookRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getBooks(){
        $result = $this->getEntityManager()
            ->createQuery(
                'SELECT  b.id FROM Application\Entity\Book b order by b.id'
            )
            ->getResult();
        return $result;
    }


    /**
     * @return array
     */
    public function getBooksDuble(){
        $result = $this->getEntityManager()
            ->createQuery(
                "

select b0.name, b0.id from Application\Entity\Book b0 
where b0.name IN(
    SELECT b.name
    FROM Application\Entity\Book b
    GROUP BY b.name
    HAVING COUNT(b.name) > 1
)

order by b0.id DESC


"
            )
            ->getResult();
        var_dump(count($result));
        return $result;
    }
}
