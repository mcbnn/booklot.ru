<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * History
 *
 * @ORM\Table(name="history", indexes={@ORM\Index(name="IDX_27BA704B6B3CA4B", columns={"id_user"})})
 * @ORM\Entity
 */
class History
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_history", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="history_id_history_seq", allocationSize=1, initialValue=1)
     */
    private $idHistory;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_type", type="integer", nullable=false)
     */
    private $idType = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="post", type="text", nullable=false)
     */
    private $post;

    /**
     * @var string
     *
     * @ORM\Column(name="request", type="text", nullable=false)
     */
    private $request;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime_created", type="datetimetz", nullable=false)
     */
    private $datetimeCreated;

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


}

