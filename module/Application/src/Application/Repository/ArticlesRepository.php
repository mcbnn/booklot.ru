<?php

namespace Application\Repository;

use Application\Entity\Articles;
use Doctrine\ORM\EntityRepository;

class ArticlesRepository extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\Query
     */
    public function getArticlesAll($get = []){

        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('c')
            ->from(Articles::class, 'c')
            ->orderBy('c.id', 'DESC');

        if(isset($get['article_name']) and !empty($get['article_name'])){
            $queryBuilder->andWhere('c.title LIKE :article_name');
            $queryBuilder->setParameter(  'article_name', '%'.$get['article_name'].'%');
        }

        if(isset($get['menu_id']) and !empty($get['menu_id'])){
            $queryBuilder->andWhere('c.menu = :menu_id');
            $queryBuilder->setParameter(  'menu_id', $get['menu_id']);
        }

        return $queryBuilder->getQuery();
    }
}
