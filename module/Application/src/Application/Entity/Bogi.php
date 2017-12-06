<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bogi
 *
 * @ORM\Table(name="bogi")
 * @ORM\Entity
 */
class Bogi
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="bogi_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="text", nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="text", nullable=false)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birth", type="date", nullable=true)
     */
    private $birth;

    /**
     * @var string
     *
     * @ORM\Column(name="sex", type="text", nullable=false)
     */
    private $sex;

    /**
     * @var string
     *
     * @ORM\Column(name="foto", type="text", nullable=false)
     */
    private $foto;

    /**
     * @var integer
     *
     * @ORM\Column(name="comments", type="bigint", nullable=false)
     */
    private $comments = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="my_book", type="integer", nullable=false)
     */
    private $myBook = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="like_book", type="integer", nullable=false)
     */
    private $likeBook = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime_reg", type="datetimetz", nullable=false)
     */
    private $datetimeReg;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime_log", type="datetimetz", nullable=false)
     */
    private $datetimeLog;

    /**
     * @var string
     *
     * @ORM\Column(name="confirm", type="text", nullable=false)
     */
    private $confirm = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="vis", type="integer", nullable=false)
     */
    private $vis = '1';

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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return \DateTime
     */
    public function getBirth()
    {
        return $this->birth;
    }

    /**
     * @param \DateTime $birth
     */
    public function setBirth($birth)
    {
        $this->birth = $birth;
    }

    /**
     * @return string
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @param string $sex
     */
    public function setSex($sex)
    {
        $this->sex = $sex;
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
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param int $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * @return \DateTime
     */
    public function getDatetimeReg()
    {
        return $this->datetimeReg;
    }

    /**
     * @param \DateTime $datetimeReg
     */
    public function setDatetimeReg($datetimeReg)
    {
        $this->datetimeReg = $datetimeReg;
    }

    /**
     * @return \DateTime
     */
    public function getDatetimeLog()
    {
        return $this->datetimeLog;
    }

    /**
     * @param \DateTime $datetimeLog
     */
    public function setDatetimeLog($datetimeLog)
    {
        $this->datetimeLog = $datetimeLog;
    }

    /**
     * @return string
     */
    public function getConfirm()
    {
        return $this->confirm;
    }

    /**
     * @param string $confirm
     */
    public function setConfirm($confirm)
    {
        $this->confirm = $confirm;
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
     * @return int
     */
    public function getMyBook()
    {
        return $this->myBook;
    }

    /**
     * @param int $myBook
     */
    public function setMyBook($myBook)
    {
        $this->myBook = $myBook;
    }

    /**
     * @return int
     */
    public function getLikeBook()
    {
        return $this->likeBook;
    }

    /**
     * @param int $likeBook
     */
    public function setLikeBook($likeBook)
    {
        $this->likeBook = $likeBook;
    }


}
