<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Text
 *
 * @ORM\Table(name="text", indexes={@ORM\Index(name="idx_10795767_num", columns={"num"}), @ORM\Index(name="idx_10795767_id_main", columns={"id_main"})})
 * @ORM\Entity(repositoryClass="Application\Repository\TextRepository")
 */
class Text
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="text_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="num", type="bigint", nullable=false)
     */
    private $num;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private $text;

    /**
     * @var \Application\Entity\Book
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Book", inversedBy="text", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_main", referencedColumnName="id")
     * })
     */
    private $idMain;

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
    public function getNum()
    {
        return $this->num;
    }

    /**
     * @param int $num
     */
    public function setNum($num)
    {
        $this->num = $num;
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
     * @return Book
     */
    public function getIdMain()
    {
        return $this->idMain;
    }

    /**
     * @param Book $idMain
     */
    public function setIdMain($idMain)
    {
        $this->idMain = $idMain;
    }

    /**
     * @return mixed
     */
    public function getTextImgServer()
    {
        return $this->changeImageServer()->text;
    }

    /**
     * Изменение фото в тексте
     * @return $this
     */
    public function changeImageServer()
    {
        if(isset($_GET['test'])){
            $this->text = preg_replace_callback(
                '/\<img.{0,300}src[\s]*\=[\s]*[\"\'](.*)[\"\'][\s]*>/isU',
                function ($matches) {
                    var_dump( $matches);die();
                    $file = explode('/', $matches[1]);
                    $file = end($file);
                    return '<img src = "'.IMAGE_URL.'/resize/200/'.$file.'" >';
                },
                $this->text
            );

           die();
        }
        $this->text = preg_replace_callback(
            '/\<img.{0,300}src[\s]*\=[\s]*"(.*)"[\s]*>/isU',
            function ($matches) {
                $file = explode('/', $matches[1]);
                $file = end($file);
                return '<img src = "'.IMAGE_URL.'/resize/200/'.$file.'" >';
            },
            $this->text
        );
        return $this;
    }
}
