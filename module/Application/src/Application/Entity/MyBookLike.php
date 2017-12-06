<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MyBookLike
 *
 * @ORM\Table(name="my_book_like", uniqueConstraints={@ORM\UniqueConstraint(name="my_book_like_id_uindex", columns={"id"})}, indexes={@ORM\Index(name="IDX_53AF052F16A2B381", columns={"book_id"}), @ORM\Index(name="IDX_53AF052FA76ED395", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="Application\Repository\MyBookLikeRepository")
 */
class MyBookLike
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="my_book_like_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

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
     * @var \Application\Entity\Bogi
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Bogi")
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
    public function setBook($book)
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
    public function setUser($user)
    {
        $this->user = $user;
    }


}

