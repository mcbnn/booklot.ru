<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CommentsFaik
 *
 * @ORM\Table(name="comments_faik", indexes={@ORM\Index(name="idx_10795676_id_book_litmir", columns={"id_book_litmir"})})
 * @ORM\Entity
 */
class CommentsFaik
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_comments_faik", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="comments_faik_id_comments_faik_seq", allocationSize=1, initialValue=1)
     */
    private $idCommentsFaik;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_book_litmir", type="bigint", nullable=false)
     */
    private $idBookLitmir;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="text", nullable=false)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="foto", type="text", nullable=false)
     */
    private $foto;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_user", type="bigint", nullable=false)
     */
    private $idUser;

    /**
     * @var \Application\Entity\Book
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Book", inversedBy="commentsFaik", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="book_id", referencedColumnName="id")
     * })
     */
    private $book;

    /**
     * @return int
     */
    public function getIdCommentsFaik()
    {
        return $this->idCommentsFaik;
    }

    /**
     * @param int $idCommentsFaik
     */
    public function setIdCommentsFaik($idCommentsFaik)
    {
        $this->idCommentsFaik = $idCommentsFaik;
    }

    /**
     * @return int
     */
    public function getIdBookLitmir()
    {
        return $this->idBookLitmir;
    }

    /**
     * @param int $idBookLitmir
     */
    public function setIdBookLitmir($idBookLitmir)
    {
        $this->idBookLitmir = $idBookLitmir;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getFoto()
    {
        return $this->foto;
    }

    /**
     * @param string $foto
     */
    public function setFoto($foto)
    {
        $this->foto = $foto;
    }

    /**
     * @return int
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * @param int $idUser
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
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


}
