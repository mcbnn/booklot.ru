<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comments
 *
 * @ORM\Table(name="comments", uniqueConstraints={@ORM\UniqueConstraint(name="comments_id_uindex", columns={"id"})}, indexes={@ORM\Index(name="IDX_5F9E962A16A2B381", columns={"book_id"}), @ORM\Index(name="IDX_5F9E962AA76ED395", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="Application\Repository\CommentsRepository")
 */
class Comments
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="comments_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime", type="datetime", nullable=true)
     */
    private $datetime = 'now()';

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=500, nullable=false)
     */
    private $text;

    /**
     * @var integer
     *
     * @ORM\Column(name="vis", type="integer", nullable=false)
     */
    private $vis = '1';

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
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @param \DateTime $datetime
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
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

