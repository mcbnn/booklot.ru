<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BookFiles
 *
 * @ORM\Table(name="book_files", indexes={@ORM\Index(name="idx_10795645_id_book", columns={"id_book"})})
 * @ORM\Entity
 */
class BookFiles
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_book_files", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="book_files_id_book_files_seq", allocationSize=1, initialValue=1)
     */
    private $idBookFiles;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="text", nullable=false)
     */
    private $type;

    /**
     * @var \Application\Entity\Book
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Book", inversedBy="files", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_book", referencedColumnName="id")
     * })
     */
    private $idBook;

    /**
     * @return int
     */
    public function getIdBookFiles()
    {
        return $this->idBookFiles;
    }

    /**
     * @param int $idBookFiles
     */
    public function setIdBookFiles($idBookFiles)
    {
        $this->idBookFiles = $idBookFiles;
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return Book
     */
    public function getIdBook()
    {
        return $this->idBook;
    }

    /**
     * @param Book $idBook
     */
    public function setIdBook($idBook)
    {
        $this->idBook = $idBook;
    }


}
