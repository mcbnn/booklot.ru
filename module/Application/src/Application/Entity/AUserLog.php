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
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param \DateTime $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param string $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    /**
     * @return string
     */
    public function getBot()
    {
        return $this->bot;
    }

    /**
     * @param string $bot
     */
    public function setBot($bot)
    {
        $this->bot = $bot;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }


}
