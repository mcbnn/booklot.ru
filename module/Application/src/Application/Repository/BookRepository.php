<?php

namespace Application\Repository;

use Application\Entity\MZhanr;
use Application\Entity\Zhanr;
use Application\Entity\Book;
use Doctrine\ORM\EntityRepository;

class BookRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getBooks()
    {
        $result = $this->getEntityManager()->createQuery(
                'SELECT  b.id FROM Application\Entity\Book b ORDER BY b.id'
            )->getResult();

        return $result;
    }

    public function getBoksOneZhanr($alias)
    {

        if ($alias == null) {
            return;
        }

        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('b')
            ->from(Book::class, 'b')
            ->innerJoin(
                Zhanr::class,
                'z',
                'WITH',
                'b.id = z.idMain'
            )
            ->innerJoin(MZhanr::class, 'mz', 'WITH', 'mz.id = z.idMenu')
            ->where('mz.alias = :alias')
            ->andWhere('b.vis = :vis')
            ->andWhere('b.foto != :foto')
            ->andWhere('b.textSmall is not null')
            ->orderBy('b.stars', 'DESC')
            ->addOrderBy('b.countStars', 'DESC')
            ->addOrderBy('b.visit', 'DESC')
            ->setMaxResults(10)
            ->setParameters(
                [
                    'alias' => $alias,
                    'vis'   => 1,
                    'foto'  => 'nofoto.jpg',
                ]
            );

        return $queryBuilder->getQuery()->getResult();
    }
}
