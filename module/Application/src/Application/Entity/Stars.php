<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stars
 *
 * @ORM\Table(name="stars", indexes={@ORM\Index(name="IDX_11DC02C40C5BF33", columns={"id_book"})})
 * @ORM\Entity
 */
class Stars
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="stars_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="stars", type="integer", nullable=false)
     */
    private $stars;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="text", nullable=false)
     */
    private $ip;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime_created", type="datetimetz", nullable=false)
     */
    private $datetimeCreated;

    /**
     * @var \Application\Entity\Book
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Book")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_book", referencedColumnName="id")
     * })
     */
    private $idBook;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getStars()
    {
        return $this->stars;
    }

    /**
     * @param int $stars
     */
    public function setStars($stars)
    {
        $this->stars = $stars;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return \DateTime
     */
    public function getDatetimeCreated()
    {
        return $this->datetimeCreated;
    }

    /**
     * @param \DateTime $datetimeCreated
     */
    public function setDatetimeCreated($datetimeCreated)
    {
        $this->datetimeCreated = $datetimeCreated;
    }

    /**
     * @return Book
     */
    public function getIdBook()
    {
        return $this->idBook;
    }

    /**
     * @param Book $idBook
     */
    public function setIdBook($idBook)
    {
        $this->idBook = $idBook;
    }


}
