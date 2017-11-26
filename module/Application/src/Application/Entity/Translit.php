<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Translit
 *
 * @ORM\Table(name="translit", indexes={@ORM\Index(name="idx_10795782_id_menu", columns={"id_menu"}), @ORM\Index(name="idx_10795782_id_main", columns={"id_main"})})
 * @ORM\Entity
 */
class Translit
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="translit_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \Application\Entity\MTranslit
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MTranslit")
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

