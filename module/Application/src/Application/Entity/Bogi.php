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


}

