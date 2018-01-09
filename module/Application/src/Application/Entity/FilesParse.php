<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FilesParse
 *
 * @ORM\Table(name="files_parse")
 * @ORM\Entity
 */
class FilesParse
{
    /**
     * @var integer
     *
     * @ORM\Column(name="file_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="files_parse_id_file_seq", allocationSize=1, initialValue=1)
     */
    private $fileId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var \Application\Entity\Book
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Book")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     * })
     */
    private $bookId;

    /**
     * @return int
     */
    public function getFileId ()
    {
        return $this->fileId;
    }

    /**
     * @param int $fileId
     */
    public function setFileId ($fileId)
    {
        $this->fileId = $fileId;
    }

    /**
     * @return string
     */
    public function getName ()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName ($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getType ()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType ($type)
    {
        $this->type = $type;
    }

    /**
     * @return Book
     */
    public function getBookId ()
    {
        return $this->bookId;
    }

    /**
     * @param Book $bookId
     */
    public function setBookId ($bookId)
    {
        $this->bookId = $bookId;
    }
    
}
