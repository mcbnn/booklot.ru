<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TextDop
 *
 * @ORM\Table(name="text_dop", indexes={@ORM\Index(name="idx_10795776_num", columns={"num"}), @ORM\Index(name="idx_10795776_id_main", columns={"id_main"}), @ORM\Index(name="idx_10795776_id_text", columns={"id_text"})})
 * @ORM\Entity
 */
class TextDop
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="text_dop_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="num", type="integer", nullable=false)
     */
    private $num;

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
     * @var \Application\Entity\Text
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Text")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_text", referencedColumnName="id")
     * })
     */
    private $idText;


}

