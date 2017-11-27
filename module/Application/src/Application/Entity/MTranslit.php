<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MTranslit
 *
 * @ORM\Table(name="m_translit", indexes={@ORM\Index(name="idx_10795713_id_litmir", columns={"id_litmir"})})
 * @ORM\Entity
 */
class MTranslit
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="m_translit_id_seq", allocationSize=1, initialValue=1)
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
