<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdStat
 *
 * @ORM\Table(name="ad_stat", uniqueConstraints={@ORM\UniqueConstraint(name="ad_stat_ad_stat_id_uindex", columns={"ad_stat_id"})}, indexes={@ORM\Index(name="IDX_9877AD444F34D596", columns={"ad_id"})})
 * @ORM\Entity
 */
class AdStat
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ad_stat_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="ad_stat_ad_stat_id_seq", allocationSize=1, initialValue=1)
     */
    private $adStatId;

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="string", length=100, nullable=true)
     */
    private $info;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime", type="datetimetz", nullable=false)
     */
    private $datetime;

    /**
     * @var \Application\Entity\Ad
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Ad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ad_id", referencedColumnName="ad_id")
     * })
     */
    private $ad;

    /**
     * @var string
     *
     * @ORM\Column(name="page", type="string", length=500, nullable=true)
     */
    private $page;

    /**
     * @return int
     */
    public function getAdStatId()
    {
        return $this->adStatId;
    }

    /**
     * @param int $adStatId
     */
    public function setAdStatId($adStatId)
    {
        $this->adStatId = $adStatId;
    }

    /**
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param string $info
     */
    public function setInfo($info)
    {
        $this->info = $info;
    }

    /**
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @param \DateTime $datetime
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    }

    /**
     * @return Ad
     */
    public function getAd()
    {
        return $this->ad;
    }

    /**
     * @param Ad $ad
     */
    public function setAd($ad)
    {
        $this->ad = $ad;
    }

    /**
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param string $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }


}

