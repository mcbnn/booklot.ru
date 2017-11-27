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
     * @ORM\ManyToOne(targetEntity="Application\Entity\Book")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_book", referencedColumnName="id")
     * })
     */
    private $idBook;


}
