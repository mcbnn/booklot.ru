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
        return $this->changeImageServer()->ins_adv()->text;
    }

    /**
     * Изменение фото в тексте
     * @return $this
     */
    public function changeImageServer()
    {
        $this->text = preg_replace_callback(
            '/\<img[\s]*src[\s]*\=[\s]*"(.*)"[\s]*>/isU',
            function ($matches) {
                $file = explode('/', $matches[1]);
                $file = end($file);
                return '<img src = "https://www.image.booklot.ru/resize/200/'.$file.'" >';
            },
            $this->text
        );
        return $this;
    }

    /**
     * Добавление рекламы google в текст
     * @return mixed|string
     */
    public function ins_adv()
    {
        $text = $this->text;
        if (!$text) {
            $this->text = "<div class = 'alert alert-warning'>Сайт находится в наполнение, по поводу замечаний пишите на почту <a href = 'mailto:mc_bnn@mail.ru?subject=Вопрос по замечанию на сайте'>mc_bnn@mail.ru</a></div>";
            return $this;
        }
        $text = preg_replace("/\<a(.*)\>(.*)\<\/a\>/iU", "$2", $text);
        $txt = '';
        $i = rand(0,2);
        $block
            = "<div class = 'text-center'><script async src=\"//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js\"></script>
<ins class=\"adsbygoogle\"
     style=\"display:block\"
     data-ad-client=\"ca-pub-2745956118385780\"
     data-ad-slot=\"3283834548\"
     data-ad-format=\"auto\"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script></div>";
        $arr = array();
        $arr1 = array();
        $tag = "</p>";
        $pieces = explode($tag, $text);
        foreach ($pieces as $piece) {
            $txt .= $piece;
            if (strlen(strip_tags($txt)) > 5000) {
                // добавляем в конец разделитель $tag,
                // т.к. в массив попадают строки без него.
                $arr[] = substr($piece, -250).$tag;
                $arr1[] = substr($piece, -250).$tag.$block;
                $txt = '';
                $i += 1;
            }
            if ($i == 1) {
                break;
            }
        }
        $this->text = str_replace($arr, $arr1, $text);
        return $this;
    }

}
