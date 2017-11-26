<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comments
 *
 * @ORM\Table(name="comments", indexes={@ORM\Index(name="IDX_5F9E962A6B3CA4B", columns={"id_user"})})
 * @ORM\Entity
 */
class Comments
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="comments_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_parrent", type="bigint", nullable=false)
     */
    private $idParrent = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_content", type="bigint", nullable=false)
     */
    private $idContent;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_menu", type="integer", nullable=false)
     */
    private $idMenu = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime", type="datetimetz", nullable=false)
     */
    private $datetime;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="text", nullable=false)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="browser", type="text", nullable=false)
     */
    private $browser;

    /**
     * @var integer
     *
     * @ORM\Column(name="ban", type="integer", nullable=false)
     */
    private $ban = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="vis", type="integer", nullable=false)
     */
    private $vis = '1';

    /**
     * @var \Application\Entity\Bogi
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Bogi")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     * })
     */
    private $idUser;


}

