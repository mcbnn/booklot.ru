<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MyBookStatusName
 *
 * @ORM\Table(name="my_book_status_name", uniqueConstraints={@ORM\UniqueConstraint(name="my_book_status_name_status_id_uindex", columns={"status_id"})})
 * @ORM\Entity
 */
class MyBookStatusName
{
    /**
     * @var integer
     *
     * @ORM\Column(name="status_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="my_book_status_name_status_id_seq", allocationSize=1, initialValue=1)
     */
    private $statusId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="src", type="string", length=50, nullable=false)
     */
    private $src;

    /**
     * @return int
     */
    public function getStatusId()
    {
        return $this->statusId;
    }

    /**
     * @param int $statusId
     */
    public function setStatusId($statusId)
    {
        $this->statusId = $statusId;
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

    /**
     * @return string
     */
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * @param string $src
     */
    public function setSrc($src)
    {
        $this->src = $src;
    }


}

