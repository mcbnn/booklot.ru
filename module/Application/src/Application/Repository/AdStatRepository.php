<?php

namespace Application\Repository;

use Application\Entity\AdStat;
use Doctrine\ORM\EntityRepository;

class AdStatRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getDateCount($ad_id = null)
    {
        if (!$ad_id) {
            return [];
        }
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select(
            'date(adstat.datetime)  as datetime, count(adstat.datetime) as total_count'
        )->from(AdStat::class, 'adstat')->where('adstat.ad = :ad')->andWhere(
                'adstat.info != \'127.0.0.1\''
            )->groupBy('datetime')->orderBy('datetime', 'ASC')->setParameter(
                'ad',
                $ad_id
            );

        return $queryBuilder->getQuery()->getResult();
    }

    public function getStatAll($ad = null, $get = null)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('adstat')
            ->from(AdStat::class, 'adstat')
            ->where('adstat.info != \'127.0.0.1\'')
            ->orderBy('adstat.ad', 'DESC');
        if ($ad) {
            $queryBuilder->andWhere('adstat.ad = :ad');
            $queryBuilder->setParameter('ad', $ad);
        }

        if(isset($get['ad_stat_id']) and !empty($get['ad_stat_id'])){
            $queryBuilder->andWhere('adstat.adStatId = :ad_stat_id');
            $queryBuilder->setParameter(  'ad_stat_id', $get['ad_stat_id']);
        }
        if(isset($get['info']) and !empty($get['info'])){
            $info = htmlspecialchars(mb_strtolower($get['info']."%", 'UTF-8'));
            $queryBuilder->andWhere('adstat.info = :info');
            $queryBuilder->setParameter(  'info', $info);
        }
        if(isset($get['datetime']) and !empty($get['datetime'])){
            $queryBuilder->andWhere('date(adstat.datetime) = :datetime');
            $queryBuilder->setParameter(  'datetime', $get['datetime']);
        }
        if(isset($get['page']) and !empty($get['page'])){
            $page = htmlspecialchars(mb_strtolower($get['page']."%", 'UTF-8'));
            $queryBuilder->andWhere('LOWER(adstat.page) LIKE :page');
            $queryBuilder->setParameter(  'page', $page);
        }
        return $queryBuilder->getQuery();
    }
}
