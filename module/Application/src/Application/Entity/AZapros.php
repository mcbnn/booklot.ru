<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AZapros
 *
 * @ORM\Table(name="a_zapros", indexes={@ORM\Index(name="idx_10795610_name", columns={"name"})})
 * @ORM\Entity
 */
class AZapros
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="a_zapros_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="poiskovik", type="text", nullable=false)
     */
    private $poiskovik;

    /**
     * @var integer
     *
     * @ORM\Column(name="count", type="bigint", nullable=false)
     */
    private $count;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="text", nullable=false)
     */
    private $url;


}

