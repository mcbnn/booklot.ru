<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comments
 *
 * @ORM\Table(name="comments", indexes={@ORM\Index(name="IDX_5F9E962A6B3CA4B", columns={"id_user"})})
 * @ORM\Entity(repositoryClass="Application\Repository\CommentsRepository")
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
     * @var \Application\Entity\Book
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Book")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="id")
     * })
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
    public function getIdParrent()
    {
        return $this->idParrent;
    }

    /**
     * @param int $idParrent
     */
    public function setIdParrent($idParrent)
    {
        $this->idParrent = $idParrent;
    }

    /**
     * @return Book
     */
    public function getIdContent()
    {
        return $this->idContent;
    }

    /**
     * @param Book $idContent
     */
    public function setIdContent($idContent)
    {
        $this->idContent = $idContent;
    }

    /**
     * @return int
     */
    public function getIdMenu()
    {
        return $this->idMenu;
    }

    /**
     * @param int $idMenu
     */
    public function setIdMenu($idMenu)
    {
        $this->idMenu = $idMenu;
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

    /**
     * @return string
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * @param string $browser
     */
    public function setBrowser($browser)
    {
        $this->browser = $browser;
    }

    /**
     * @return int
     */
    public function getBan()
    {
        return $this->ban;
    }

    /**
     * @param int $ban
     */
    public function setBan($ban)
    {
        $this->ban = $ban;
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
     * @return Bogi
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * @param Bogi $idUser
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
    }


}
