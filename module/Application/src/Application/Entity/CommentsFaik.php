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


}

