<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MZhanr
 *
 * @ORM\Table(name="m_zhanr", indexes={@ORM\Index(name="idx_10795722_id_main", columns={"id_main"}), @ORM\Index(name="idx_10795722_alias", columns={"alias"})})
 * @ORM\Entity
 */
class MZhanr
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="m_zhanr_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_main", type="integer", nullable=false)
     */
    private $idMain;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="text", nullable=true)
     */
    private $icon;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="text", nullable=false)
     */
    private $alias;

    /**
     * @var string
     *
     * @ORM\Column(name="route", type="text", nullable=true)
     */
    private $route;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="text", nullable=true)
     */
    private $action;

    /**
     * @var integer
     *
     * @ORM\Column(name="count_book", type="bigint", nullable=false)
     */
    private $countBook = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="vis", type="integer", nullable=false)
     */
    private $vis = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="keywords", type="text", nullable=true)
     */
    private $keywords;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="seo_text", type="text", nullable=false)
     */
    private $seoText;


}

