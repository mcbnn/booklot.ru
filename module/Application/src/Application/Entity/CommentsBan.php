<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CommentsBan
 *
 * @ORM\Table(name="comments_ban", indexes={@ORM\Index(name="IDX_D92A9F66B3CA4B", columns={"id_user"}), @ORM\Index(name="IDX_D92A9F6F457ABF", columns={"id_comments"})})
 * @ORM\Entity
 */
class CommentsBan
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="comments_ban_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="text", nullable=false)
     */
    private $url;

    /**
     * @var \Application\Entity\Bogi
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Bogi")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     * })
     */
    private $idUser;

    /**
     * @var \Application\Entity\Comments
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Comments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_comments", referencedColumnName="id")
     * })
     */
    private $idComments;


}

