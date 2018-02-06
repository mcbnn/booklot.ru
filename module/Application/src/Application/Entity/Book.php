<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Book
 *
 * @ORM\Table(name="book", indexes={@ORM\Index(name="idx_10795631_stars", columns={"stars"}), @ORM\Index(name="idx_10795631_date_add", columns={"date_add"}), @ORM\Index(name="idx_10795631_count_stars", columns={"count_stars"}), @ORM\Index(name="idx_10795631_kol_str", columns={"kol_str"}), @ORM\Index(name="idx_10795631_vis", columns={"vis"}), @ORM\Index(name="idx_10795631_name", columns={"name"}), @ORM\Index(name="idx_10795631_alias", columns={"alias"})})
 * @ORM\Entity(repositoryClass="Application\Repository\BookRepository")
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
     * @var integer
     *
     * @ORM\Column(name="id_book_litmir", type="integer", nullable=false)
     */
    private $idBookLitmir = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="kol_str", type="bigint", nullable=false)
     */
    private $kolStr = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="year", type="bigint", nullable=false)
     */
    private $year = 0;

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
    private $foto = 'nofoto.jpg';

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
    private $vis = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="visit", type="bigint", nullable=false)
     */
    private $visit = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="sort", type="bigint", nullable=true)
     */
    private $sort = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="route", type="text", nullable=false)
     */
    private $route = 'home/genre/one/book';

    /**
     * @var float
     *
     * @ORM\Column(name="stars", type="float", precision=10, scale=0, nullable=false)
     */
    private $stars = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="url_partner", type="text", nullable=false)
     */
    private $urlPartner;

    /**
     * @var string
     *
     * @ORM\Column(name="type_files", type="text", nullable=true)
     */
    private $typeFiles;

    /**
     * @var string
     *
     * @ORM\Column(name="n_alias_menu", type="text", nullable=true)
     */
    private $nAliasMenu;

    /**
     * @var string
     *
     * @ORM\Column(name="n_s", type="text", nullable=true)
     */
    private $nS;

    /**
     * @var string
     *
     * @ORM\Column(name="name_zhanr", type="text", nullable=true)
     */
    private $nameZhanr;

    /**
     * @var integer
     *
     * @ORM\Column(name="count_stars", type="integer", nullable=false)
     */
    private $countStars = 0;

    /**
     * @ORM\ManyToMany(targetEntity="MAvtor")
     * @ORM\JoinTable(name="avtor",
     *      joinColumns={@ORM\JoinColumn(name="id_main", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_menu", referencedColumnName="id")}
     *      )
     */
    private $avtor;

    /**
     * @var \Application\Entity\MZhanr
     *
     * @ORM\ManyToOne(targetEntity="\Application\Entity\MZhanr", inversedBy="book")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="menu_id", referencedColumnName="id")
     * })
     */
    private $menu;

    /**
     * @var \Application\Entity\MSerii
     *
     * @ORM\ManyToMany(targetEntity="MSerii")
     * @ORM\JoinTable(name="serii",
     *      joinColumns={@ORM\JoinColumn(name="id_main", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_menu", referencedColumnName="id")}
     *      )
     */
    private $serii;

    /**
     * @var \Application\Entity\MTranslit
     *
     * @ORM\ManyToMany(targetEntity="MTranslit")
     * @ORM\JoinTable(name="translit",
     *      joinColumns={@ORM\JoinColumn(name="id_main", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_menu", referencedColumnName="id")}
     *      )
     */
    private $translit;

    /**
     * @var \Application\Entity\CommentsFaik
     * @ORM\OneToMany(targetEntity="\Application\Entity\CommentsFaik", mappedBy="book")
     */
    private $commentsFaik;

    /**
     * @var \Application\Entity\Soder
     * @ORM\OneToMany(targetEntity="\Application\Entity\Soder", mappedBy="idMain")
     * @ORM\OrderBy({"num" = "ASC"})
     */
    private $soder;

    /**
     * @var \Application\Entity\Text
     * @ORM\OneToMany(targetEntity="\Application\Entity\Text", mappedBy="idMain", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"num" = "ASC"})
     */
    private $text;

    /**
     * @var \Application\Entity\BookFiles
     * @ORM\OneToMany(targetEntity="\Application\Entity\BookFiles", mappedBy="idBook")
     */
    private $files;

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
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getTextSmall()
    {
        return $this->textSmall;
    }

    /**
     * @param string $textSmall
     */
    public function setTextSmall($textSmall)
    {
        $this->textSmall = $textSmall;
    }

    /**
     * @return int
     */
    public function getIdBookLitmir()
    {
        return $this->idBookLitmir;
    }

    /**
     * @param int $idBookLitmir
     */
    public function setIdBookLitmir($idBookLitmir)
    {
        $this->idBookLitmir = $idBookLitmir;
    }

    /**
     * @return int
     */
    public function getKolStr()
    {
        return $this->kolStr;
    }

    /**
     * @param int $kolStr
     */
    public function setKolStr($kolStr)
    {
        $this->kolStr = $kolStr;
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param int $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param string $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @return string
     */
    public function getLangOr()
    {
        return $this->langOr;
    }

    /**
     * @param string $langOr
     */
    public function setLangOr($langOr)
    {
        $this->langOr = $langOr;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * @param string $isbn
     */
    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;
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
     * @return \DateTime
     */
    public function getDateAdd()
    {
        return $this->dateAdd;
    }

    /**
     * @param \DateTime $dateAdd
     */
    public function setDateAdd($dateAdd)
    {
        $this->dateAdd = $dateAdd;
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
    public function getVisit()
    {
        return $this->visit;
    }

    /**
     * @param int $visit
     */
    public function setVisit($visit)
    {
        $this->visit = $visit;
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param string $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @return float
     */
    public function getStars()
    {
        return $this->stars;
    }

    /**
     * @param float $stars
     */
    public function setStars($stars)
    {
        $this->stars = $stars;
    }

    /**
     * @return string
     */
    public function getUrlPartner()
    {
        return $this->urlPartner;
    }

    /**
     * @param string $urlPartner
     */
    public function setUrlPartner($urlPartner)
    {
        $this->urlPartner = $urlPartner;
    }

    /**
     * @return string
     */
    public function getTypeFiles()
    {
        return $this->typeFiles;
    }

    /**
     * @param string $typeFiles
     */
    public function setTypeFiles($typeFiles)
    {
        $this->typeFiles = $typeFiles;
    }

    /**
     * @return string
     */
    public function getNAliasMenu()
    {
        return $this->nAliasMenu;
    }

    /**
     * @param string $nAliasMenu
     */
    public function setNAliasMenu($nAliasMenu)
    {
        $this->nAliasMenu = $nAliasMenu;
    }

    /**
     * @return string
     */
    public function getNS()
    {
        return $this->nS;
    }

    /**
     * @param string $nS
     */
    public function setNS($nS)
    {
        $this->nS = $nS;
    }

    /**
     * @return string
     */
    public function getNameZhanr()
    {
        return $this->nameZhanr;
    }

    /**
     * @param string $nameZhanr
     */
    public function setNameZhanr($nameZhanr)
    {
        $this->nameZhanr = $nameZhanr;
    }

    /**
     * @return int
     */
    public function getCountStars()
    {
        return $this->countStars;
    }

    /**
     * @param int $countStars
     */
    public function setCountStars($countStars)
    {
        $this->countStars = $countStars;
    }

    /**
     * @return mixed
     */
    public function getAvtor()
    {
        return $this->avtor;
    }

    /**
     * @param mixed $avtor
     */
    public function setAvtor($avtor)
    {
        $this->avtor = $avtor;
    }

    /**
     * @return mixed
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @param mixed $menu
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;
    }

    /**
     * @return mixed
     */
    public function getSerii()
    {
        return $this->serii;
    }

    /**
     * @param mixed $serii
     */
    public function setSerii($serii)
    {
        $this->serii = $serii;
    }

    /**
     * @return mixed
     */
    public function getTranslit()
    {
        return $this->translit;
    }

    /**
     * @param mixed $translit
     */
    public function setTranslit($translit)
    {
        $this->translit = $translit;
    }

    /**
     * @return CommentsFaik
     */
    public function getCommentsFaik()
    {
        return $this->commentsFaik;
    }

    /**
     * @param CommentsFaik $commentsFaik
     */
    public function setCommentsFaik($commentsFaik)
    {
        $this->commentsFaik = $commentsFaik;
    }

    /**
     * @return Soder
     */
    public function getSoder()
    {
        return $this->soder;
    }

    /**
     * @param Soder $soder
     */
    public function setSoder($soder)
    {
        $this->soder = $soder;
    }

    /**
     * @return Text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param Text $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return BookFiles
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param BookFiles $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * @param null $type
     *
     * @return null|string|void
     */
    public function getMetaTitle($type = null)
    {
        if(!$type) return;
        $title = null;
        switch($type){
            case 'genre':
                $title = "Книга ".$this->getName().". Жанр - ".$this->getMenu()
                        ->getParent()
                        ->getName()." - ".$this->getMenu()
                        ->getName();

                break;
            case 'problem-avtor':
                $title = "Книга ".$this->getName().". Жанр - ".$this->getMenu()
                        ->getParent()
                        ->getName()." - ".$this->getMenu()
                        ->getName();
                break;
            case 'serii':
                var_dump($this->getSerii());die();
                $title = "Книга ".$this->getName().". Серия - ".$this->getSerii(
                    )->current()->getName();
                break;
            case 'avtor':
                $title = "Книга ".$this->getName().". Автор - ".$this->getAvtor(
                    )->current()->getName();
                break;
            case 'translit':

                if(count($this->getTranslit())){
                    $title = "Книга ".$this->getName().". Переводчик - ".$this->getTranslit()->current()->getName();
                }
                else{
                    $title = "Книга ".$this->getName().".";
                }
                break;
        }

        return $title;
    }

}
