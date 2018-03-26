<?php

namespace Application\Model;

use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceManager;
use Application\Controller\MainController;
use Application\Entity\Book;
use Application\Entity\MZhanr;
use Application\Entity\MAvtor;
use Application\Entity\Avtor;
use Application\Entity\MTranslit;
use Application\Entity\Translit;
use Application\Entity\MSerii;
use Application\Entity\Serii;
use Application\Entity\Text;
use Application\Entity\Soder;
use Application\Entity\BookFiles;
use Application\Entity\FilesParse;

class DocumentFb2
{
    private $err = null;
    private $images = [];

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em = null;
    /**
     * @var null|ServiceManager
     */
    protected $sm = null;

    public function __construct (EntityManager $em, ServiceManager $sm)
    {
        /** @var \Doctrine\ORM\EntityManager em */
        $this->em = $em;
        /** @var \Zend\ServiceManager\ServiceManager sm */
        $this->sm = $sm;
    }

    /**
     * @param \DOMDocument $doc
     *
     * @return $this|void
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function convert (\DOMDocument $doc)
    {
        if (!$doc) {
            return;
        }
        $this->fb2 = basename($doc->baseURI);
        $this->parseDomSetParams($doc);
        $this->downloadImage();
        $this->textPagesConvert($doc);
        $this->saveModel();

        return $this;
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveZipFile()
    {
        $config = $this->sm->get('config');
        $zip = new \ZipArchive();
        $this->name_zip_file = $this->alias.'_'.$this->id;
        $zip_name = $this->name_zip_file.'.zip';
        $destination = $config['UPLOAD_DIR'].'newsave/fb2/'.$zip_name;
        if ($zip->open($destination, \ZIPARCHIVE::CREATE) !== true) {
           $this->err['zip_destination'] = 'Проблема с созданием каталога zip';
        }
        $zip->addFile(
            $config['UPLOAD_DIR'].'newsave/convert/'.$this->fb2,
            $this->alias.'__booklot.ru____.fb2'
        );
        $zip->close();
        $book_files = new BookFiles();
        $book_files->setIdBook($this->em->getRepository(Book::class)->find($this->id));
        $book_files->setName($this->name_zip_file);
        $book_files->setType('fb2');
        $this->em->persist($book_files);
        $this->em->flush();
    }

    /**
     * @return null
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveModel()
    {
        ini_set('display_errors', true);
        /** @var  $book  \Application\Entity\Book */
        $book = $this->em->getRepository(Book::class)->findOneBy(['name' => $this->name]);
        if($book){
            $this->err['bookCount'] = $this->name." Данная книга уже существует";
            return null;
        }
        $mainController = new MainController();
        $alias = $mainController->trans($this->name);

        do {
            /** @var $findBy \Application\Entity\Book */
            $findBy = $this->em->getRepository(Book::class)
                ->findOneBy(
                    ['alias' => $alias]
                );
            $count = 0;
            if ($findBy) {
                $alias = $alias.'-';
                $count = 1;
            };
        } while ($count != 0);
        /** @var  $mzhanr  \Application\Entity\MZhanr */
        $mzhanr = $this->em->getRepository(MZhanr::class)->findOneBy(['genre' => $this->genre]);
        if(!$mzhanr){
            $mzhanr = $this->em->getRepository(MZhanr::class)->find(639);
            $this->err['mzhanr'] = "Не найден жанр. {$this->genre}";
        }
        $book = new Book();
        $book->setAlias($alias);
        $book->setName($this->name);
        $book->setMenu($mzhanr);
        $book->setNAliasMenu($mzhanr->getAlias());
        $book->setNameZhanr($mzhanr->getName());
        $book->setNS($mzhanr->getParent()->getAlias());
        $book->setVis(0);
        $book->setDateAdd(new \DateTime());
        if(isset($this->images[$this->coverpage]['name'])){
            $foto = $this->images[$this->coverpage]['name'];
        }
        else{
            $foto = "nofoto.jpg";
        }
        $book->setFoto($foto);
        $book->setIsbn($this->isbn);
        $book->setYear($this->year);
        $book->setTextSmall($this->annotation);
        $book->setLangOr($this->src_lang);
        $book->setLang($this->lang);
        $book->setKolStr(count($this->text)+1);
        $this->em->persist($book);
        /** @var authors */
        if(count($this->authors)){
            foreach($this->authors as $author){
                /** @var  $mavtor  \Application\Entity\MAvtor */
                $mavtor = $this->em->getRepository(MAvtor::class)->findOneBy(['name' => $author]);
                if(!$mavtor){
                    $mavtor = new MAvtor();
                    $mavtor->setName($author);
                    $mavtor->setAlias($mainController->trans($this->name));
                    $this->em->persist($mavtor);
                }
                /** @var  $avtor \Application\Entity\Avtor */
                $avtor = new Avtor();
                $avtor->setIdMain($book);
                $avtor->setIdMenu($mavtor);
                $this->em->persist($avtor);
            }
        }
        if($this->sequence){
            /** @var  $mserii  \Application\Entity\MSerii */
            $mserii = $this->em->getRepository(MSerii::class)->findOneBy(['name' => $this->sequence]);
            if(!$mserii){
                $mserii = new MSerii();
                $mserii->setName($this->sequence);
                $mserii->setAlias($mainController->trans($this->sequence));
                $this->em->persist($mserii);
            }
            /** @var  $serii \Application\Entity\Serii */
            $serii = new Serii();
            $serii->setIdMain($book);
            $serii->setIdMenu($mserii);
            $this->em->persist($serii);
        }
        if(count($this->translators)){
            foreach($this->translators as $translit){
                /** @var  $mtranslit  \Application\Entity\MTranslit */
                $mtranslit = $this->em->getRepository(MTranslit::class)->findOneBy(['name' => $translit]);
                if(!$mtranslit){
                    $mtranslit = new MTranslit();
                    $mtranslit->setName($translit);
                    $mtranslit->setAlias($mainController->trans($translit));
                    $this->em->persist($mtranslit);
                }
                /** @var  $avtor \Application\Entity\Translit */
                $translit = new Translit();
                $translit->setIdMain($book);
                $translit->setIdMenu($mtranslit);
                $this->em->persist($translit);
            }
        }
        if(count($this->text)){
            foreach($this->text as $k => $text){
                $num = $k+1;
                /** @var  $text_entity \Application\Entity\Text */
                $text_entity = new Text();
                $text_entity->setIdMain($book);
                $text_entity->setNum($num);
                $text_entity->setText($text['text']);
                $this->em->persist($text_entity);
                if(isset($text['title'])) {
                    foreach($text['title'] as $title) {
                        /** @var  $soder_entity \Application\Entity\Soder */
                        $soder_entity = new Soder();
                        $soder_entity->setNum($num);
                        $soder_entity->setName($title);
                        $soder_entity->setIdMain($book);
                        $soder_entity->setAlias($mainController->trans($title));
                        $this->em->persist($soder_entity);
                    }
                }
            }
        }
        $this->em->flush();
        $this->id = $book->getId();
        $this->saveZipFile();
        /** @var  $filse_parse \Application\Entity\FilesParse */
        $filse_parse = $this->em->getRepository(FilesParse::class)->find($this->file_id);
        $filse_parse->setBookId($book);
        $filse_parse->setType(2);
        $this->em->flush($filse_parse);
    }

    /**
     * @param $doc
     */
    public function textPagesConvert($doc)
    {
        $step = 14000;
        $outHTML = $doc->saveHTML();
        $outHTML = html_entity_decode($outHTML);

        $outHTML = preg_replace('/<description>.*<\/description>/isU', '', $outHTML);

        $outHTML = preg_replace('/<binary.*<\/binary>/is', '', $outHTML);
        $outHTML = strip_tags ($outHTML, '<empty-line><p><image><title><epigraph><image><poem><subtitle><cite><empty-line><table>');
        $outHTML = preg_replace('/<epigraph>/isU', '<div class = "fb2-epigraph">', $outHTML);
        $outHTML = preg_replace('/<\/epigraph>/is', '</div>', $outHTML);
        $outHTML = preg_replace('/<empty-line>/isU', '<div class = "fb2-empty-line">', $outHTML);
        $outHTML = preg_replace('/<\/empty-line>/is', '</div>', $outHTML);
        $outHTML = preg_replace('/<title><p>/isU', '<div class = "fb2-title">', $outHTML);
        $outHTML = preg_replace('/<\/p><\/title>/is', '</div>', $outHTML);
        $outHTML = preg_replace('/<subtitle>/isU', '<div class = "fb2-title-subtitle">', $outHTML);
        $outHTML = preg_replace('/<\/subtitle>/is', '</div>', $outHTML);
        $outHTML = preg_replace('/<poem>/isU', '<div class = "fb2-stih">', $outHTML);
        $outHTML = preg_replace('/<\/poem>/is', '</div>', $outHTML);
        $outHTML = preg_replace('/<cite>/isU', '<div class = "fb2-cite">', $outHTML);
        $outHTML = preg_replace('/<\/cite>/is', '</div>', $outHTML);

        $outHTML = preg_replace_callback(
            '/<image[\s]*l\:href="(.*)"><\/image>/is',
            function ($matches) {
                $img_text = trim($matches[1],'#');
                $img = "";
                if(isset($this->images[$img_text]['name'])) {
                    $name = $this->images[$img_text]['name'];
                    $img = "<img src = '/templates/newimg/original/$name' />";
                }
                return $img;
            },
            $outHTML
        );

        $strlen = 1;
        $arrText = [];
        if(strlen($outHTML) == 0)return;
        do{
            $max = strlen($outHTML);
            if($max < $step){
                $max = 14000;
            }
            for ($i = $step; $i <= $max; $i++) {
                $step_text = substr($outHTML, 0, $i);
                if (preg_match('/(<\/div>|<\/p>)$/isU', $step_text)) {
                    $textTitle = [];
                    $textTitle['text'] = $step_text;
                    $title = "";
                    if (preg_match_all(
                        '/<div class = "fb2-title">(.*)<\/div>/isU',
                        $step_text,
                        $title
                    )
                    ) {
                        foreach ($title[1] as $k => $v) {
                            if ($k > 1) {
                                continue;
                            }
                            $textTitle['title'][] = strip_tags($v);
                        }
                    };
                    $arrText[] = $textTitle;
                    $outHTML = substr($outHTML, $i, $max);
                    if (strlen($outHTML) <= $step) {
                        $textTitle['text'] = $outHTML;
                        $title = "";
                        if (preg_match_all(
                            '/<div class = "fb2-title">(.*)<\/div>/isU',
                            $outHTML,
                            $title
                        )
                        ) {
                            foreach ($title[1] as $k => $v) {
                                if ($k > 1) {
                                    continue;
                                }
                                $textTitle['title'][] = strip_tags($v);
                            }
                        };
                        $arrText[] = $textTitle;
                        $strlen = 0;
                    }
                    break;
                }

            }
        }
        while($strlen);
        $this->text = $arrText;
    }

    /**
     * @return null
     */
    public function getError ()
    {
        return $this->err;
    }

    /**
     * @return null
     */
    public function downloadImage ()
    {
        $config = $this->sm->get('config');
        if (!$this->images) {
            return null;
        }
        foreach ($this->images as $k => &$image) {
            $exp = explode('.', $k);
            $type_file = end($exp);
            $name_file = md5($this->alias.$k).'.'.$type_file;
            $data = base64_decode($image['id']);
            $file = $config['UPLOAD_DIR'].'newimg/original/'.$name_file;
            file_put_contents($file, $data);
            copy($file, $config['UPLOAD_DIR'].'newimg/small/'.$name_file);
            copy($file, $config['UPLOAD_DIR'].'newimg/full/'.$name_file);
            $image['name'] = $name_file;
        }
    }

    /**
     * @param null $arr
     * @param      $dom
     *
     * @return array|\DOMElement|null
     */
    public function getNodeValue ($arr = null, $dom)
    {
        if (!$arr) {
            return null;
        }
        /** @var \DOMElement $dom */
        $dom = $dom->getElementsByTagName($arr['params']['name']);
        if (!$dom->length) {
            if ($arr['params']['required']) {
                $this->err[$arr['params']['name']] = 'not value';
            }
            return null;
        }
        if (isset($arr['params']['params'])) {
            return $this->getNodeValue($arr['params'], $dom->item(0));
        }
        if ($arr['params']['type'] == 'one') {
            $el = $dom->item(0);
            if (isset($arr['params']['value'])) {
                $value = (string)$arr['params']['value'];
                return trim($el->$value);
            }
            return $el;
        } elseif ($arr['params']['type'] == 'images') {
            /** @var \DOMElement $value */
            $images = [];
            foreach ($dom as $value) {
                $images[$value->getAttribute('id')]['id'] = $value->nodeValue;
                $images[$value->getAttribute('id')]['content-type']
                    = $value->getAttribute('content-type');
            }
            return $images;
        }
        else {
            /** @var \DOMElement $v */
            $arrValue = [];
            foreach ($dom as $k => $v) {
                $arrValue[$k] = "";
                if(isset($arr['params']['child_name'])) {
                    if ($v->getElementsByTagName(
                        $arr['params']['child_name']
                    )->length
                    ) {
                        $value = (string)$arr['params']['value'];

                        $arrValue[$k] = $v->getElementsByTagName(
                            $arr['params']['child_name']
                        )->item(0)->$value;
                    }
                }
                else{

                    $arrValue[$k] = $v->nodeValue;
                }
            }
            return $arrValue;
        }
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set ($name, $value)
    {
        $this->$name = $value;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name){
        return $this->$name;
    }
    /**
     * @param $doc
     */
    public function parseDomSetParams($doc)
    {
        /** @var $doc \DOMElement */

        $this->year = $this->getNodeValue(
            [
                'params' => [
                    'name'     => 'description',
                    'type'     => 'one',
                    'required' => true,
                    'params'   => [
                        'name'     => 'publish-info',
                        'type'     => 'one',
                        'required' => true,
                        'params'   => [
                            'name'     => 'year',
                            'type'     => 'one',
                            'required' => false,
                            'value'    => 'nodeValue',
                        ],
                    ],
                ],
            ],
            $doc
        );
        $this->sequence = $this->getNodeValue(
            [
                'params' => [
                    'name'     => 'description',
                    'type'     => 'one',
                    'required' => true,
                    'params'   => [
                        'name'     => 'title-info',
                        'type'     => 'one',
                        'required' => true,
                        'params'   => [
                            'name'     => 'sequence',
                            'type'     => 'one',
                            'required' => false,
                            'value'    => 'nodeValue',
                            'child_name' => 'name',
                        ],
                    ],
                ],
            ],
            $doc
        );
        $this->genre = $this->getNodeValue(
            [
                'params' => [
                    'name'     => 'description',
                    'type'     => 'one',
                    'required' => true,
                    'params'   => [
                        'name'     => 'title-info',
                        'type'     => 'one',
                        'required' => true,
                        'params'   => [
                            'name'     => 'genre',
                            'type'     => 'one',
                            'required' => false,
                            'value'    => 'nodeValue',
                            'child_name' => 'first-name',
                        ],
                    ],
                ],
            ],
            $doc
        );
        $first_name = $this->getNodeValue(
            [
                'params' => [
                    'name'     => 'description',
                    'type'     => 'one',
                    'required' => true,
                    'params'   => [
                        'name'     => 'title-info',
                        'type'     => 'one',
                        'required' => true,
                        'params'   => [
                            'name'     => 'author',
                            'type'     => 'many',
                            'required' => false,
                            'value'    => 'nodeValue',
                            'child_name' => 'first-name',
                        ],
                    ],
                ],
            ],
            $doc
        );
        $middle_name = $this->getNodeValue(
            [
                'params' => [
                    'name'     => 'description',
                    'type'     => 'one',
                    'required' => true,
                    'params'   => [
                        'name'     => 'title-info',
                        'type'     => 'one',
                        'required' => true,
                        'params'   => [
                            'name'     => 'author',
                            'type'     => 'many',
                            'required' => false,
                            'value'    => 'nodeValue',
                            'child_name' => 'middle-name',
                        ],
                    ],
                ],
            ],
            $doc
        );
        $last_name = $this->getNodeValue(
            [
                'params' => [
                    'name'     => 'description',
                    'type'     => 'one',
                    'required' => true,
                    'params'   => [
                        'name'     => 'title-info',
                        'type'     => 'one',
                        'required' => true,
                        'params'   => [
                            'name'     => 'author',
                            'type'     => 'many',
                            'required' => false,
                            'value'    => 'nodeValue',
                            'child_name' => 'last-name',
                        ],
                    ],
                ],
            ],
            $doc
        );
        $authors = [];
        if($last_name) {
            foreach ($last_name as $k => $v) {
                $authors[] = trim($v.' '.$middle_name[$k].' '.$first_name[$k]);
            }
        }
        $this->authors = $authors;
        $first_name_translator = $this->getNodeValue(
            [
                'params' => [
                    'name'     => 'description',
                    'type'     => 'one',
                    'required' => true,
                    'params'   => [
                        'name'     => 'title-info',
                        'type'     => 'one',
                        'required' => true,
                        'params'   => [
                            'name'     => 'translator',
                            'type'     => 'many',
                            'required' => false,
                            'value'    => 'nodeValue',
                            'child_name' => 'first-name',
                        ],
                    ],
                ],
            ],
            $doc
        );
        $middle_name_translator = $this->getNodeValue(
            [
                'params' => [
                    'name'     => 'description',
                    'type'     => 'one',
                    'required' => true,
                    'params'   => [
                        'name'     => 'title-info',
                        'type'     => 'one',
                        'required' => true,
                        'params'   => [
                            'name'     => 'translator',
                            'type'     => 'many',
                            'required' => false,
                            'value'    => 'nodeValue',
                            'child_name' => 'middle-name',
                        ],
                    ],
                ],
            ],
            $doc
        );
        $last_name_translator = $this->getNodeValue(
            [
                'params' => [
                    'name'     => 'description',
                    'type'     => 'one',
                    'required' => true,
                    'params'   => [
                        'name'     => 'title-info',
                        'type'     => 'one',
                        'required' => true,
                        'params'   => [
                            'name'     => 'translator',
                            'type'     => 'many',
                            'required' => false,
                            'value'    => 'nodeValue',
                            'child_name' => 'last-name',
                        ],
                    ],
                ],
            ],
            $doc
        );
        $translators = [];
        if($last_name_translator) {
            foreach ($last_name_translator as $k => $v) {
                $translators[] = trim(
                    $v
                    .' '
                    .$middle_name_translator[$k]
                    .' '
                    .$first_name_translator[$k]
                );
            }
        }
        $this->translators = $translators;

        $this->name = $this->getNodeValue(
            [
                'params' => [
                    'name'     => 'description',
                    'type'     => 'one',
                    'required' => true,
                    'params'   => [
                        'name'     => 'title-info',
                        'type'     => 'one',
                        'required' => true,
                        'params'   => [
                            'name'     => 'book-title',
                            'type'     => 'one',
                            'required' => true,
                            'value'    => 'nodeValue',
                        ],
                    ],
                ],
            ],
            $doc
        );
        $mainController = new MainController();
        $this->alias = $mainController->trans($this->name);
        $this->annotation = $this->getNodeValue(
            [
                'params' => [
                    'name'     => 'description',
                    'type'     => 'one',
                    'required' => true,
                    'params'   => [
                        'name'     => 'title-info',
                        'type'     => 'one',
                        'required' => true,
                        'params'   => [
                            'name'     => 'annotation',
                            'type'     => 'one',
                            'required' => false,
                            'value'    => 'nodeValue',
                        ],
                    ],
                ],
            ],
            $doc
        );


        $this->lang = $this->getNodeValue(
            [
                'params' => [
                    'name'     => 'description',
                    'type'     => 'one',
                    'required' => true,
                    'params'   => [
                        'name'     => 'title-info',
                        'type'     => 'one',
                        'required' => true,
                        'params'   => [
                            'name'     => 'lang',
                            'type'     => 'one',
                            'required' => false,
                            'value'    => 'nodeValue',
                        ],
                    ],
                ],
            ],
            $doc
        );
        $this->src_lang = $this->getNodeValue(
            [
                'params' => [
                    'name'     => 'description',
                    'type'     => 'one',
                    'required' => true,
                    'params'   => [
                        'name'     => 'title-info',
                        'type'     => 'one',
                        'required' => true,
                        'params'   => [
                            'name'     => 'src-lang',
                            'type'     => 'one',
                            'required' => false,
                            'value'    => 'nodeValue',
                        ],
                    ],
                ],
            ],
            $doc
        );
        $this->city = $this->getNodeValue(
            [
                'params' => [
                    'name'     => 'description',
                    'type'     => 'one',
                    'required' => true,
                    'params'   => [
                        'name'     => 'publish-info',
                        'type'     => 'one',
                        'required' => true,
                        'params'   => [
                            'name'     => 'city',
                            'type'     => 'one',
                            'required' => false,
                            'value'    => 'nodeValue',
                        ],
                    ],
                ],
            ],
            $doc
        );
        $this->isbn = $this->getNodeValue(
            [
                'params' => [
                    'name'     => 'description',
                    'type'     => 'one',
                    'required' => true,
                    'params'   => [
                        'name'     => 'publish-info',
                        'type'     => 'one',
                        'required' => true,
                        'params'   => [
                            'name'     => 'isbn',
                            'type'     => 'one',
                            'required' => false,
                            'value'    => 'nodeValue',
                        ],
                    ],
                ],
            ],
            $doc
        );
        $item = $this->getNodeValue(
            [
                'params' => [
                    'name'     => 'description',
                    'type'     => 'one',
                    'required' => true,
                    'params'   => [
                        'name'     => 'title-info',
                        'type'     => 'one',
                        'required' => true,
                        'params'   => [
                            'name'     => 'coverpage',
                            'type'     => 'one',
                            'required' => false,
                            'params'   => [
                                'name'     => 'image',
                                'type'     => 'one',
                                'required' => false,
                            ],
                        ],
                    ],
                ],
            ],
            $doc
        );
        $this->coverpage = trim($item->getAttribute('l:href'), '#');
        $this->images = $this->getNodeValue(
            [
                'params' => [
                    'name'     => 'binary',
                    'type'     => 'images',
                    'required' => true,
                ],
            ],
            $doc
        );
    }
}