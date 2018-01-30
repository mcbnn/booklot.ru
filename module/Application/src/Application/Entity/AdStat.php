<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdStat
 *
 * @ORM\Table(name="ad_stat", uniqueConstraints={@ORM\UniqueConstraint(name="ad_stat_ad_stat_id_uindex", columns={"ad_stat_id"})})
 * @ORM\Entity
 */
class AdStat
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ad_id", type="integer", nullable=false)
     */
    private $adId;

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
     * @var \Application\Entity2\Ad
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Application\Entity2\Ad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ad_stat_id", referencedColumnName="ad_id")
     * })
     */
    private $adStat;

    /**
     * @return int
     */
    public function getAdId()
    {
        return $this->adId;
    }

    /**
     * @param int $adId
     */
    public function setAdId($adId)
    {
        $this->adId = $adId;
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
    public function getAdStat()
    {
        return $this->adStat;
    }

    /**
     * @param Ad $adStat
     */
    public function setAdStat($adStat)
    {
        $this->adStat = $adStat;
    }


}

