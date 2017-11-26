<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Opros
 *
 * @ORM\Table(name="opros")
 * @ORM\Entity
 */
class Opros
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="opros_id_seq", allocationSize=1, initialValue=1)
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
     * @ORM\Column(name="id_vk", type="text", nullable=false)
     */
    private $idVk;

    /**
     * @var integer
     *
     * @ORM\Column(name="vis", type="integer", nullable=false)
     */
    private $vis;


}

