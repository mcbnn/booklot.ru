<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Avtor
 *
 * @ORM\Table(name="avtor", indexes={@ORM\Index(name="idx_10795587_id_main", columns={"id_main"}), @ORM\Index(name="idx_10795587_id_menu", columns={"id_menu"})})
 * @ORM\Entity
 */
class Avtor
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="avtor_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \Application\Entity\MAvtor
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MAvtor")
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


}

