<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MSerii
 *
 * @ORM\Table(name="m_serii", indexes={@ORM\Index(name="idx_10795704_id_litmir", columns={"id_litmir"})})
 * @ORM\Entity
 */
class MSerii
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="m_serii_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_litmir", type="bigint", nullable=false)
     */
    private $idLitmir;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="text", nullable=true)
     */
    private $alias;


}

