<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MZhanr
 *
 * @ORM\Table(name="m_zhanr", indexes={@ORM\Index(name="idx_10795722_id_main", columns={"id_main"}), @ORM\Index(name="idx_10795722_alias", columns={"alias"})})
 * @ORM\Entity(repositoryClass="Application\Repository\MZhanrRepository")
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
     * @ORM\Column(name="id_main", type="integer", nullable=true)
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

    /**
     * @var integer
     *
     * @ORM\Column(name="see", type="integer", nullable=true)
     */
    private $see = '1';

    /**
     * @ORM\OneToOne(targetEntity="MZhanr")
     * @ORM\JoinColumn(name="id_main", referencedColumnName="id")
     */
    private $parent;

    /**
     * @var string
     *
     * @ORM\Column(name="genre", type="text", nullable=true)
     */
    private $genre;

    /**
     * @var \Application\Entity\Book
     * @ORM\OneToMany(targetEntity="\Application\Entity\Book", mappedBy="menu", fetch="EXTRA_LAZY", fetch="EXTRA_LAZY")
     */
    private $book;

    /**
     * @var integer
     *
     * @ORM\Column(name="old", type="integer", nullable=false)
     */
    private $old = '0';

    public function __construct() {

    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idMain
     *
     * @param integer $idMain
     *
     * @return MZhanr
     */
    public function setIdMain($idMain)
    {
        $this->idMain = $idMain;

        return $this;
    }

    /**
     * Get idMain
     *
     * @return integer
     */
    public function getIdMain()
    {
        return $this->idMain;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return MZhanr
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set icon
     *
     * @param string $icon
     *
     * @return MZhanr
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set alias
     *
     * @param string $alias
     *
     * @return MZhanr
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set route
     *
     * @param string $route
     *
     * @return MZhanr
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set action
     *
     * @param string $action
     *
     * @return MZhanr
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set countBook
     *
     * @param integer $countBook
     *
     * @return MZhanr
     */
    public function setCountBook($countBook)
    {
        $this->countBook = $countBook;

        return $this;
    }

    /**
     * Get countBook
     *
     * @return integer
     */
    public function getCountBook()
    {
        return $this->countBook;
    }

    /**
     * Set vis
     *
     * @param integer $vis
     *
     * @return MZhanr
     */
    public function setVis($vis)
    {
        $this->vis = $vis;

        return $this;
    }

    /**
     * Get vis
     *
     * @return integer
     */
    public function getVis()
    {
        return $this->vis;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     *
     * @return MZhanr
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return MZhanr
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set seoText
     *
     * @param string $seoText
     *
     * @return MZhanr
     */
    public function setSeoText($seoText)
    {
        $this->seoText = $seoText;

        return $this;
    }

    /**
     * Get seoText
     *
     * @return string
     */
    public function getSeoText()
    {
        return $this->seoText;
    }

    /**
     * Set see
     *
     * @param integer $see
     *
     * @return MZhanr
     */
    public function setSee($see)
    {
        $this->see = $see;

        return $this;
    }

    /**
     * Get see
     *
     * @return integer
     */
    public function getSee()
    {
        return $this->see;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return string
     */
    public function getGenre ()
    {
        return $this->genre;
    }

    /**
     * @param string $genre
     */
    public function setGenre ($genre)
    {
        $this->genre = $genre;
    }

    /**
     * @return Book
     */
    public function getBook()
    {
        return $this->book;
    }

    /**
     * @param Book $book
     */
    public function setBook($book)
    {
        $this->book = $book;
    }

    /**
     * @return int
     */
    public function getOld()
    {
        return $this->old;
    }

    /**
     * @param int $old
     */
    public function setOld(int $old)
    {
        $this->old = $old;
    }

}
