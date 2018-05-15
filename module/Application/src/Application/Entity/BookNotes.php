<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BookNotes
 *
 * @ORM\Table(name="book_notes", uniqueConstraints={@ORM\UniqueConstraint(name="book_notes_notes_id_uindex", columns={"notes_id"})}, indexes={@ORM\Index(name="IDX_FD9B3F516A2B381", columns={"book_id"})})
 * @ORM\Entity
 */
class BookNotes
{
    /**
     * @var integer
     *
     * @ORM\Column(name="notes_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="book_notes_notes_id_seq", allocationSize=1, initialValue=1)
     */
    private $notesId;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=100, nullable=false)
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=500, nullable=true)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=100, nullable=true)
     */
    private $title;

    /**
     * @var \Application\Entity\Book
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Book")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     * })
     */
    private $book;

    /**
     * @return int
     */
    public function getNotesId(): int
    {
        return $this->notesId;
    }

    /**
     * @param int $notesId
     */
    public function setNotesId(int $notesId)
    {
        $this->notesId = $notesId;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink(string $link)
    {
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return Book
     */
    public function getBook(): Book
    {
        return $this->book;
    }

    /**
     * @param Book $book
     */
    public function setBook(Book $book)
    {
        $this->book = $book;
    }


}

