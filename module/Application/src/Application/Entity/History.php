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

    /**
     * @return int
     */
    public function getIdHistory()
    {
        return $this->idHistory;
    }

    /**
     * @param int $idHistory
     */
    public function setIdHistory($idHistory)
    {
        $this->idHistory = $idHistory;
    }

    /**
     * @return int
     */
    public function getIdType()
    {
        return $this->idType;
    }

    /**
     * @param int $idType
     */
    public function setIdType($idType)
    {
        $this->idType = $idType;
    }

    /**
     * @return string
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param string $post
     */
    public function setPost($post)
    {
        $this->post = $post;
    }

    /**
     * @return string
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param string $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return \DateTime
     */
    public function getDatetimeCreated()
    {
        return $this->datetimeCreated;
    }

    /**
     * @param \DateTime $datetimeCreated
     */
    public function setDatetimeCreated($datetimeCreated)
    {
        $this->datetimeCreated = $datetimeCreated;
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
