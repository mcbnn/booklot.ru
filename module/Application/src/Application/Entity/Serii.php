<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Serii
 *
 * @ORM\Table(name="serii", indexes={@ORM\Index(name="idx_10795742_id_menu", columns={"id_menu"}), @ORM\Index(name="idx_10795742_id_main", columns={"id_main"})})
 * @ORM\Entity
 */
class Serii
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="serii_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \Application\Entity\MSerii
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MSerii")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_menu", referencedColumnName="id")
     * })
     */
    private $idMenu;

    /**
     * @var \Application\Entity\Book
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Book")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_main", referencedColumnName="id")
     * })
     */
    private $idMain;

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
     * @return MSerii
     */
    public function getIdMenu()
    {
        return $this->idMenu;
    }

    /**
     * @param MSerii $idMenu
     */
    public function setIdMenu($idMenu)
    {
        $this->idMenu = $idMenu;
    }

    /**
     * @return Book
     */
    public function getIdMain()
    {
        return $this->idMain;
    }

    /**
     * @param Book $idMain
     */
    public function setIdMain($idMain)
    {
        $this->idMain = $idMain;
    }


}
