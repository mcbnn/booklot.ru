<?php

namespace Application\Repository;

use Application\Entity\Text;
use Doctrine\ORM\EntityRepository;

class TextRepository extends EntityRepository
{
    /**
     * @param null $book_id
     *
     * @return \Doctrine\ORM\Query|void
     */
    public function getTexts($book_id = null)
    {
        if(!$book_id)return;
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('t')
            ->from(Text::class, 't')
            ->where('t.idMain = :book_id')
            ->orderBy('t.num', 'ASC')
            ->setParameter('book_id', $book_id);
        return $queryBuilder->getQuery();
    }
}
