<?php

namespace Application\Repository;

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

    public $ttl = 300000;

    public function findOneByRep(array $where)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('b')
            ->from(Book::class, 'b');
        foreach ($where as $k => $v){
            $v = htmlspecialchars(mb_strtolower("$v", 'UTF-8'));
            if($k != 'vis'){
                $queryBuilder->andWhere("LOWER(b.$k) = :$k");
            }
            else{
                $queryBuilder->andWhere("b.$k = :$k");
            }
            $queryBuilder->setParameter($k, $v);
        }
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @return array
     */
    public function getDubleAlias()
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('b.alias')
            ->from(Book::class, 'b')
            ->groupBy('b.alias')
            ->having('count(b.alias) > 1')
        ;
        return $queryBuilder->getQuery()->getResult();
    }
    /**
     * @return array
     */
    public function getMenuNull()
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('b')
            ->from(Book::class, 'b')
            ->where('b.menu IS  NULL ');
        ;
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param int $count
     *
     * @return array
     */
    public function getPopularBooks($count = 10)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('b')
            ->from(Book::class, 'b')
            ->where('b.vis = 1')
            ->andWhere('b.nAliasMenu != \'erotika\'')
            ->andWhere('b.foto != \'nofoto.jpg\'')
            ->orderBy('b.countStars', 'DESC')
            ->addOrderBy('b.id', 'DESC')
            ->setMaxResults($count)
        ;
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function getResults(){
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager
            ->createQuery("Select b from Application\Entity\Book  b where b.vis = 1 order by b.id");
        return $queryBuilder->iterate();
    }

    /**
     * @param $alias
     *
     * @return array
     */
    public function findLikeAlias($alias){
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $alias = htmlspecialchars(mb_strtolower("$alias", 'UTF-8'));
        $queryBuilder->select('b')
            ->from(Book::class, 'b')
            ->where('LOWER(b.alias) = :alias')
            ->orderBy('b.alias', 'ASC')
            ->setParameter('alias', $alias);
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function checkAliasBook()
    {
        $result = $this->getEntityManager()->createQuery(
            "
                    select  b from Application\Entity\Book  b
                    where  (
                    select count(1) from Application\Entity\Book  b1
                    where LOWER(b1.alias) LIKE LOWER(b.alias)
              
                    ) > 1
                    
                  
"
        )
            ->getResult();

        return $result;
    }

    /**
     * @return array
     */
    public function disBookZhanr()
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('b.menu')
            ->from(Book::class, 'b')
            ->addSelect('count(1) ')
            ->groupBy('b.menu')
            ->setMaxResults(10);
        $result = $queryBuilder->getQuery()->getResult();
        return $result;
    }
    /**
     * @param null $value
     *
     * @return array|void
     */
    public function findByLangOr($value = null){
        if(!$value)return;
        $value = htmlspecialchars(mb_strtolower("$value%", 'UTF-8'));
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('b')
            ->from(Book::class, 'b')
            ->select('b.langOr')
            ->andWhere('LOWER(b.langOr) like :langOr')
            ->setParameter('langOr', $value)
            ->distinct()
            ->setMaxResults(10);
        $result = $queryBuilder->getQuery()->getResult();
        return $result;
    }
    /**
     * @param null $value
     *
     * @return array|void
     */
    public function findByLang($value = null){
        if(!$value)return;
        $value = htmlspecialchars(mb_strtolower("$value%", 'UTF-8'));
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('b')
            ->from(Book::class, 'b')
            ->select('b.lang')
            ->andWhere('LOWER(b.lang) like :lang')
            ->setParameter('lang', $value)
            ->distinct()
            ->setMaxResults(10);
        $result = $queryBuilder->getQuery()->getResult();
        return $result;
    }
    /**
     * @param null $value
     *
     * @return array|void
     */
    public function findByCity($value = null){
        if(!$value)return;
        $value = htmlspecialchars(mb_strtolower("%$value%", 'UTF-8'));
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('b')
            ->from(Book::class, 'b')
            ->select('b.city')
            ->andWhere('LOWER(b.city) like :city')
            ->setParameter('city', $value)
            ->distinct()
            ->setMaxResults(10);
        $result = $queryBuilder->getQuery()->getResult();
        return $result;
    }

    /**
     * @param null $value
     *
     * @return array|void
     */
    public function findByISBN($value = null){
        if(!$value)return;
        $value = htmlspecialchars(mb_strtolower("$value%", 'UTF-8'));
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('b')
            ->from(Book::class, 'b')
            ->select('b.isbn')
            ->andWhere('LOWER(b.isbn) like :isbn')
            ->setParameter('isbn', $value)
            ->distinct()
            ->setMaxResults(10);
        $result = $queryBuilder->getQuery()->getResult();
        return $result;
    }
    /**
     * @param null $value
     *
     * @return array|void
     */
    public function findByYears($value = null){
        if(!$value)return;
        $value = htmlspecialchars(mb_strtolower("$value%", 'UTF-8'));
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('b')
            ->from(Book::class, 'b')
            ->select('b.year')
            ->andWhere('LOWER(b.year) like :year')
            ->setParameter('year', $value)
            ->distinct()
            ->setMaxResults(10);

        $result = $queryBuilder->getQuery()->getResult();
        return $result;
    }

    /**
     * @param null $value
     *
     * @return array|void
     */
    public function findByBookName($value = null){
        if(!$value)return;
        $value = htmlspecialchars(mb_strtolower("%$value%", 'UTF-8'));
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('b')
            ->from(Book::class, 'b')
            ->andWhere('LOWER(b.name) like :name')
            ->andWhere('b.vis = :vis')
            ->setParameter('name', $value)
            ->setParameter('vis', 1)
            ->setMaxResults(10);
        $result = $queryBuilder
            ->getQuery()
            ->useResultCache(true, $this->ttl)
            ->setCacheable(true)
            ->getResult();
        return $result;
    }
    /**
     * @param null $arraySort
     * @param null $where
     *
     * @return \Doctrine\ORM\Query
     */
    public function getBooksQuery($arraySort = null, $where = null, $cache = true){

        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('b')
            ->from(Book::class, 'b');
        foreach($arraySort['order'] as $k => $v){
               $queryBuilder->addOrderBy($k, $v);
        }
        if($where['where']){
            foreach($where['where'] as $k => $v){
                switch ($k){
                    case 'ma_name':
                        $queryBuilder->innerJoin(
                        Avtor::class,
                        'a',
                        'WITH',
                        'b.id = a.idMain'
                        );
                        $queryBuilder->innerJoin(
                            MAvtor::class,
                            'ma',
                            'WITH',
                            'ma.id = a.idMenu');
                        break;
                    case 'ms_name':
                        $queryBuilder->innerJoin(
                        Serii::class,
                        's',
                        'WITH',
                        'b.id = s.idMain'
                        );
                        $queryBuilder->innerJoin(
                            MSerii::class,
                            'ms',
                            'WITH',
                            'ms.id = s.idMenu'
                        );
                        break;
                    case 'mt_name':
                        $queryBuilder->innerJoin(
                        Translit::class,
                        't',
                        'WITH',
                        'b.id = t.idMain'
                        );
                        $queryBuilder->innerJoin(
                            MTranslit::class,
                            'mt',
                            'WITH',
                            'mt.id = t.idMenu');
                        break;
                }
                if($v['operator'] == 'or'){
                    $queryBuilder->orWhere("{$v['column']} {$v['type']} :{$k}");
                }
                else{
                    $queryBuilder->andWhere("{$v['column']} {$v['type']} :{$k}");
                }

                $queryBuilder->setParameter($k,"{$v['value']}");
            }
        }
        if($cache){
            return $queryBuilder
                ->getQuery()
                ->useResultCache(true, $this->ttl)
                ->setCacheable(true);
        }
        else{
            return $queryBuilder
                ->getQuery();
        }

    }
    /**
     * @param null $book
     *
     * @return array|void
     */
    public function similarTranslit($book = null, $translit = null){
        if ($book == null) {
            return;
        }
        if ($translit == null) {
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
                    'alias' => $translit->getAlias(),
                    'id' => $book->getId(),
                    'vis'   => 1
                ]
            );
        return $queryBuilder
            ->getQuery()
            ->useResultCache(true, $this->ttl)
            ->setCacheable(true)
            ->getResult();
    }

    /**
     * @param null $book
     *
     * @return array|void
     */
    public function similarAvtor($book = null, $avtor = null){
        if ($book == null) {
            return;
        }
        if ($avtor == null) {
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
                    'alias' => $avtor->getAlias(),
                    'id' => $book->getId(),
                    'vis'   => 1
                ]
            );
        return $queryBuilder
            ->getQuery()
            ->useResultCache(true, $this->ttl)
            ->setCacheable(true)
            ->getResult();
    }

    /**
     * @param null $book
     *
     * @return array|void
     */
    public function similarSerii($book = null, $serii = null){
        if ($book == null) {
            return;
        }
        if ($serii == null) {
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
                    'alias' => $serii->getAlias(),
                    'id' => $book->getId(),
                    'vis'   => 1
                ]
            );
        return $queryBuilder
            ->getQuery()
            ->useResultCache(true, $this->ttl)
            ->setCacheable(true)
            ->getResult();
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
        return $queryBuilder
            ->getQuery()
            ->useResultCache(true, $this->ttl)
            ->setCacheable(true)
            ->getResult();
    }
    /**
     * @return array
     */
    public function getBooks()
    {
        $result = $this->getEntityManager()->createQuery(
                'SELECT  b.id FROM Application\Entity\Book b ORDER BY b.id'
            )
            ->getResult();

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
            ->where('b.nAliasMenu = :alias')
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

        return $queryBuilder
            ->getQuery()
            ->useResultCache(true, $this->ttl)
            ->setCacheable(true)
            ->getResult();

    }
}
