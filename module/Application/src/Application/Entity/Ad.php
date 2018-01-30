<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ad
 *
 * @ORM\Table(name="ad", uniqueConstraints={@ORM\UniqueConstraint(name="ad_ad_id_uindex", columns={"ad_id"})})
 * @ORM\Entity
 */
class Ad
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ad_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="ad_ad_id_seq", allocationSize=1, initialValue=1)
     */
    private $adId;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=1000, nullable=true)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=200, nullable=true)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="vis", type="integer", nullable=true)
     */
    private $vis = '1';

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
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return int
     */
    public function getVis()
    {
        return $this->vis;
    }

    /**
     * @param int $vis
     */
    public function setVis($vis)
    {
        $this->vis = $vis;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


}

