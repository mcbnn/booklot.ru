<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AUserLog
 *
 * @ORM\Table(name="a_user_log", uniqueConstraints={@ORM\UniqueConstraint(name="a_user_log_id_uindex", columns={"id"})})
 * @ORM\Entity
 */
class AUserLog
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="a_user_log_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_user", type="bigint", nullable=false)
     */
    private $idUser;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetimetz", nullable=false)
     */
    private $time = 'now()';

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="text", nullable=false)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="session_id", type="text", nullable=false)
     */
    private $sessionId;

    /**
     * @var string
     *
     * @ORM\Column(name="user_agent", type="text", nullable=false)
     */
    private $userAgent;

    /**
     * @var string
     *
     * @ORM\Column(name="bot", type="text", nullable=false)
     */
    private $bot;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="text", nullable=false)
     */
    private $ip = '0';


}

