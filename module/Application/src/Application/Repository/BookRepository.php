<?php

namespace Application\Repository;

use Application\Entity\MZhanr;
use Application\Entity\Zhanr;
use Application\Entity\Serii;
use Application\Entity\MSerii;
use Application\Entity\Avtor;
use Application\Entity\MAvtor;
use Application\Entity\Translit;
use Application\Entity\MTranslit;
use Application\Entity\Book;
use Doctrine\ORM\EntityRepository;

class BookRepository extends EntityRepository
{
    /**
     * @param null $book
     *
     * @return array|void
     */
    public function similarTranslit($book = null){
        if ($book == null) {
            return;
        }
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('b')
            ->from(Book::class, 'b')
            ->innerJoin(
                Translit::class,
                't',
                'WITH',
                'b.id = t.idMain'
            )
            ->innerJoin(
                MTranslit::class,
                'mt',
                'WITH',
                'mt.id = t.idMenu')
            ->where('mt.alias = :alias')
            ->andWhere('b.vis = :vis')
            ->andWhere('b.id != :id')
            ->orderBy('b.id', 'DESC')
            ->setMaxResults(3)
            ->setParameters(
                [
                    'alias' => $book->getTranslit()->current()->getAlias(),
                    'id' => $book->getId(),
                    'vis'   => 1
                ]
            );
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param null $book
     *
     * @return array|void
     */
    public function similarAvtor($book = null){
        if ($book == null) {
            return;
        }
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('b')
            ->from(Book::class, 'b')
            ->innerJoin(
                Avtor::class,
                'a',
                'WITH',
                'b.id = a.idMain'
            )
            ->innerJoin(
                MAvtor::class,
                'ma',
                'WITH',
                'ma.id = a.idMenu')
            ->where('ma.alias = :alias')
            ->andWhere('b.vis = :vis')
            ->andWhere('b.id != :id')
            ->orderBy('b.id', 'DESC')
            ->setMaxResults(3)
            ->setParameters(
                [
                    'alias' => $book->getAvtor()->current()->getAlias(),
                    'id' => $book->getId(),
                    'vis'   => 1
                ]
            );
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param null $book
     *
     * @return array|void
     */
    public function similarSerii($book = null){
        if ($book == null) {
            return;
        }
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('b')
            ->from(Book::class, 'b')
            ->innerJoin(
                Serii::class,
                's',
                'WITH',
                'b.id = s.idMain'
            )
            ->innerJoin(
                MSerii::class,
                'ms',
                'WITH',
                'ms.id = s.idMenu')
            ->where('ms.alias = :alias')
            ->andWhere('b.vis = :vis')
            ->andWhere('b.id != :id')
            ->orderBy('b.id', 'DESC')
            ->setMaxResults(3)
            ->setParameters(
                [
                    'alias' => $book->getSerii()->current()->getAlias(),
                    'id' => $book->getId(),
                    'vis'   => 1
                ]
            );
        return $queryBuilder->getQuery()->getResult();
    }
    /**
     * @param null $book
     *
     * @return array|void
     */
    public function similar($book = null){
        if ($book == null) {
            return;
        }
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('b')
            ->from(Book::class, 'b')
            ->where('b.nAliasMenu = :alias')
            ->andWhere('b.vis = :vis')
            ->andWhere('b.id != :id')
            ->orderBy('b.id', 'DESC')
            ->setMaxResults(3)
            ->setParameters(
                [
                    'alias' => $book->getNAliasMenu(),
                    'id' => $book->getId(),
                    'vis'   => 1
                ]
            );
        return $queryBuilder->getQuery()->getResult();
    }
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

    /**
     * @param $alias
     *
     * @return array|void
     */
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
