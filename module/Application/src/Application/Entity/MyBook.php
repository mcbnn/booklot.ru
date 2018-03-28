<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MyBook
 *
 * @ORM\Table(name="my_book", uniqueConstraints={@ORM\UniqueConstraint(name="my_book_id_uindex", columns={"id"})}, indexes={@ORM\Index(name="IDX_BC28A6516A2B381", columns={"book_id"}), @ORM\Index(name="IDX_BC28A65A76ED395", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="Application\Repository\MyBookRepository")
 */
class MyBook
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="my_book_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \Application\Entity\Book
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Book", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     * })
     */
    private $book;

    /**
     * @var \Application\Entity\Bogi
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Bogi", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

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
     * @return Book
     */
    public function getBook()
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

    /**
     * @return Bogi
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param Bogi $user
     */
    public function setUser(Bogi $user)
    {
        $this->user = $user;
    }


}

