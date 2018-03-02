<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MTranslit
 *
 * @ORM\Table(name="m_translit", indexes={@ORM\Index(name="idx_10795713_id_litmir", columns={"id_litmir"})})
 * @ORM\Entity(repositoryClass="Application\Repository\MTranslitRepository")
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

    /**
     * @var \Application\Entity\Book
     * @ORM\ManyToMany(targetEntity="Application\Entity\Book")
     * @ORM\JoinTable(name="translit",
     *      joinColumns={@ORM\JoinColumn(name="id_menu", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_main", referencedColumnName="id", unique=true)}
     *      )
     */
    private $books;

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
     * @return int
     */
    public function getIdLitmir()
    {
        return $this->idLitmir;
    }

    /**
     * @param int $idLitmir
     */
    public function setIdLitmir($idLitmir)
    {
        $this->idLitmir = $idLitmir;
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
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return Book
     */
    public function getBooks()
    {
        return $this->books;
    }

    /**
     * @param Book $books
     */
    public function setBooks(Book $books)
    {
        $this->books = $books;
    }

}
