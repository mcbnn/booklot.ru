<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Book
 *
 * @ORM\Table(name="book", indexes={@ORM\Index(name="idx_10795631_stars", columns={"stars"}), @ORM\Index(name="idx_10795631_date_add", columns={"date_add"}), @ORM\Index(name="idx_10795631_count_stars", columns={"count_stars"}), @ORM\Index(name="idx_10795631_kol_str", columns={"kol_str"}), @ORM\Index(name="idx_10795631_vis", columns={"vis"}), @ORM\Index(name="idx_10795631_name", columns={"name"}), @ORM\Index(name="idx_10795631_alias", columns={"alias"})})
 * @ORM\Entity
 */
class Book
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="book_id_seq", allocationSize=1, initialValue=1)
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
     * @ORM\Column(name="alias", type="text", nullable=true)
     */
    private $alias;

    /**
     * @var string
     *
     * @ORM\Column(name="text_small", type="text", nullable=true)
     */
    private $textSmall;

    /**
     * @var string
     *
     * @ORM\Column(name="txt", type="text", nullable=false)
     */
    private $txt;

    /**
     * @var string
     *
     * @ORM\Column(name="fb2", type="text", nullable=false)
     */
    private $fb2;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_book_litmir", type="bigint", nullable=false)
     */
    private $idBookLitmir;

    /**
     * @var integer
     *
     * @ORM\Column(name="type_litmir", type="integer", nullable=false)
     */
    private $typeLitmir = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="kol_str", type="bigint", nullable=false)
     */
    private $kolStr;

    /**
     * @var integer
     *
     * @ORM\Column(name="year", type="bigint", nullable=false)
     */
    private $year;

    /**
     * @var string
     *
     * @ORM\Column(name="lang", type="text", nullable=false)
     */
    private $lang;

    /**
     * @var string
     *
     * @ORM\Column(name="lang_or", type="text", nullable=false)
     */
    private $langOr;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="text", nullable=false)
     */
    private $city;

    /**
     * @var integer
     *
     * @ORM\Column(name="year_print", type="integer", nullable=false)
     */
    private $yearPrint;

    /**
     * @var string
     *
     * @ORM\Column(name="isbn", type="text", nullable=false)
     */
    private $isbn;

    /**
     * @var string
     *
     * @ORM\Column(name="foto", type="text", nullable=false)
     */
    private $foto;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetimetz", nullable=false)
     */
    private $dateAdd;

    /**
     * @var integer
     *
     * @ORM\Column(name="vis", type="integer", nullable=false)
     */
    private $vis = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="visit", type="bigint", nullable=false)
     */
    private $visit = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="reiting", type="integer", nullable=false)
     */
    private $reiting;

    /**
     * @var integer
     *
     * @ORM\Column(name="str_litmir", type="bigint", nullable=false)
     */
    private $strLitmir;

    /**
     * @var integer
     *
     * @ORM\Column(name="sort", type="bigint", nullable=true)
     */
    private $sort;

    /**
     * @var string
     *
     * @ORM\Column(name="route", type="text", nullable=false)
     */
    private $route;

    /**
     * @var float
     *
     * @ORM\Column(name="stars", type="float", precision=10, scale=0, nullable=false)
     */
    private $stars = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="url_partner", type="text", nullable=false)
     */
    private $urlPartner;

    /**
     * @var integer
     *
     * @ORM\Column(name="count_stars", type="integer", nullable=false)
     */
    private $countStars = '0';


}

