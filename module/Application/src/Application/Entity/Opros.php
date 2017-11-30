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
    public function getIdVk()
    {
        return $this->idVk;
    }

    /**
     * @param string $idVk
     */
    public function setIdVk($idVk)
    {
        $this->idVk = $idVk;
    }

    /**
     * @return int
     */
    public function getVis()
    {
        return $this->vis;
    }

    /**
     * @param int $vis
     */
    public function setVis($vis)
    {
        $this->vis = $vis;
    }


}
