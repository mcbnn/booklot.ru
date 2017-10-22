<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Expression;
use Application\Controller\MainController;
use Application\Controller\ParserController;

class TechnicalController extends AbstractActionController
{

    public static $text = "";
    public $index = 0;

    public function speedAction(){
        $start = microtime(true);
        $sm = $this->getServiceLocator();
        $page = 1;
        var_dump(microtime(true) - $start);

        $order = "book.date_add DESC";
        $sort = $this->params()->fromQuery('sort', null);
        $direction = ($this->params()->fromQuery('direction', 'desc') == 'desc') ? 'desc' : 'asc';
        if ($sort and in_array($sort, [
                'visit',
                'name',
                'date_add',
                'stars',
                'kol_str'
            ])) {
            $order = "book.$sort $direction";
            if ($sort == 'stars') {
                $order = "book.$sort $direction , book.count_stars DESC";
            }

        }

        $where = "";

            $sum = $sm->get('Application\Model\MZhanrTable')->columnSummTable()->fetchAll(false);
            $sum = $sum->current();


            $book = $sm->get('Application\Model\BookTable')->joinZhanr()->joinMZhanr()->joinMZhanrParent()->joinColumn([
                'id',
                'foto',
                'alias',
                'visit',
                'name',
                'text_small',
                'stars',
                'count_stars',
                'date_add',
                'kol_str',
                'lang'
            ])->limit(24)->offset($page * 24 - 24)->fetchAll(false, $order, $where);
        var_dump(microtime(true) - $start);
            $pag = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\NullFill($sum->summBook));
            $pag->setCurrentPageNumber($page);
            $pag->setItemCountPerPage(24);
        var_dump(microtime(true) - $start);

        $where = "route = 'home'";
        $menu = $sm->get('Application\Model\MZhanrTable')->fetchAll(false, false, $where)->current();
        var_dump(microtime(true) - $start);
        $vm = new ViewModel([
            'book' => $book,
            'pag'  => $pag
        ]);
        $vm->setTemplate('application/index/index');
die();
        return $vm;

    }

    public function replacenameAction(){

        $sm = $this->getServiceLocator();
        $books = $sm->get('Application\Model\BookTable')->fetchAll(false, false, 'book.name LIKE "%&%"');
        foreach($books as $v){
            var_dump($v->name);
            $arr = [];
            $arr['name'] = html_entity_decode($v->name);
            $sm->get('Application\Model\BookTable')->save($arr, ['id' => $v->id]);


        }
        var_dump($books->count());        die();

    }

    public function parserAction(){

        $p = new ParserController;
        $sm = $this->getServiceLocator();
        $p->parser($sm);
        
    }

    public function bookAliasDubleAction()
    {
        die();
        $sm = $this->getServiceLocator();
        $sql = "SELECT count(*) as c, book.id FROM `book` group by alias having c > 1";
        $book = $sm->get('Application\Model\BookTable')->columnCountTwoTable()->fetchAll(false, false, false, false, 'alias', 'c > 1');
        foreach ($book as $k => $v) {

            $where = "alias = '{$v->alias}'";
            $book2 = $sm->get('Application\Model\BookTable')->fetchAll(false, false, $where);
            foreach ($book2 as $v1) {

                $alias = $sm->get('Main')->trans($v1->name);
                $alias = $sm->get('Main')->checkDubleAlias($alias, 'book', $v1->id);
                $arr = array();
                $arr['alias'] = $alias;
                $where = array();
                $where['id'] = $v1->id;
                var_dump($arr);
                $sm->get('Application\Model\BookTable')->save($arr, $where);


            }

        }

    }

    public function genRandAction()
    {

        ini_set('display_errors', 1);
        $sm = $this->getServiceLocator();
        $arr['sort'] = 0;

        $sm->get('Application\Model\BookTable')->save($arr, 1);

        $a = $sm->get('Application\Model\BookTable')->fetchAll(false, ' RAND() ', 'vis = 1 and foto != "nofoto.jpg" and foto != ""', 24);
        foreach ($a as $v) {
            $arr = array();
            $arr['sort'] = rand(1, 1000);
            $where = array();
            $where['id'] = $v->id;
            $sm->get('Application\Model\BookTable')->save($arr, $where);

        }
        die();

    }

    public function countBookAction()
    {

        $sm = $this->getServiceLocator();
        ini_set('display_errors', 1);
        $where = "book.vis = '1'";
        $fetchMenuObject = $sm->get('Application\Model\ZhanrTable')->joinBook()->columnCountTable()->fetchAll(false, false, $where, false, 'id_menu');
        foreach ($fetchMenuObject as $v) {

            $arr = array();
            $where = array();
            $arr['count_book'] = $v->countBook;
            $where['id'] = $v->id_menu;
            $sm->get('Application\Model\MZhanrTable')->save($arr, $where);

        }

    }

    public function changeSoderAction()
    {
        die();
        ini_set("memory_limit", "-1");
        ini_set('max_execution_time', 9999999);
        $where = false;
        //$where = "id = 25367";
        $sm = $this->getServiceLocator();
        $fetchMenuObject = $sm->get('Application\Model\BookTable')->fetchAll(false, false, $where);
        $fetchMenuArray = array();
        foreach ($fetchMenuObject as $k => $v) {
            //print_r($k);
            $where = array();
            //$v->arr['alias'] = $v->arr['id']."-". $sm->get('Main')->trans($v->arr['name']);
            $v->arr['alias'] = $sm->get('Main')->trans($v->arr['name']);
            $where['id'] = $v->arr['id'];
            //var_dump($v->arr);
            $sm->get('Application\Model\MAvtorTable')->save($v->arr, $where);
        }
        die();
    }

    public function checkTableAction()
    {
        die();
        ini_set('display_errors', 1);
        $sm = $this->getServiceLocator();
        $book = $sm->get('Application\Model\BookTable')->fetchAll(false, false, false, '1550');
        foreach ($book as $v) {
            //var_dump($v);
            $kol_str = $v->kol_str;
            $id = $v->id;
            $where = "text.id_main = '{$id}'";
            $text = $sm->get('Application\Model\TextTable')->fetchAll(false, false, $where);
            $text_count = $text->count();
            if ($kol_str > $text_count) {
                var_dump($where);
                var_dump($id);
                var_dump($text_count);
                var_dump($kol_str);

            }

        }
        var_dump($book->count());
        die();
    }

    public function checkCountArray($arr)
    {

        if (count($arr) >= 3000) {
            $this->index++;
            $this->insertFileSitemap($arr);
            $arr = array();
        }
        return $arr;

    }

    public function sitemapAction()
    {

        global $site;
        ini_set('display_errors', 1);
        ini_set("memory_limit", "-1");
        ini_set('max_execution_time', 310600);
        chdir('public/sitemap');

        foreach (glob("*.xml") as $filename) {
            unlink($filename);
        }

        $sm = $this->getServiceLocator();
        $arr = array();

        //переводчик
	$where = "alias != ''";
        $translit = $sm->get('Application\Model\MTranslitTable')->fetchAll(false, false, $where);
        foreach ($translit as $k => $v) {

            $ar['loc'] = $this->getServiceLocator()->get('ViewHelperManager')->get('url')->__invoke('home/translit/one',
                array('subdomain' => $site, 'alias_menu' => $v->alias));
            $ar['lastmod'] = date("Y-m-d");
            $ar['changefreq'] = "monthly";
            $ar['priority'] = "0.8";
            $arr[] = $ar;
            $arr = $this->checkCountArray($arr);
            $where = "translit.id_menu = '{$v->id}'";
            $mtranslit = $sm->get('Application\Model\MTranslitTable')->joinTranslit()->joinBook()->fetchAll(false, false, $where);

            if ($mtranslit->count() != 0) {
                foreach ($mtranslit as $v1) {
                    $ar['loc'] = $this->getServiceLocator()->get('ViewHelperManager')->get('url')->
                    __invoke('home/translit/one/book', array('subdomain' => $site, 'alias_menu' => $v->alias,
                        'book' => $v1->book_alias));
                    $ar['lastmod'] = date("Y-m-d");
                    $ar['changefreq'] = "never";
                    $ar['priority'] = "0.6";
                    $arr[] = $ar;
                    $arr = $this->checkCountArray($arr);
                    $where = "soder.id_main = {$v1->book_id}";
                    $soder = $sm->get('Application\Model\SoderTable')->fetchAll(false, 'id ASC', $where);
                    if ($soder->count() != 0) {
                        foreach ($soder as $v2) {
                            $ar['loc'] = $this->getServiceLocator()->get('ViewHelperManager')->get('url')->
                            __invoke('home/translit/one/book/content', array('subdomain' => $site,
                                'alias_menu' => $v->alias, 'book' => $v1->book_alias, 'content' => $v2->alias));
                            $ar['lastmod'] = date("Y-m-d");
                            $ar['changefreq'] = "never";
                            $ar['priority'] = "0.4";
                            $arr[] = $ar;
                            $arr = $this->checkCountArray($arr);
                        }
                    }
                }
            }

        }

        //серии

        $serii = $sm->get('Application\Model\MSeriiTable')->fetchAll(false);
        foreach ($serii as $k => $v) {

            $ar['loc'] = $this->getServiceLocator()->get('ViewHelperManager')->get('url')->__invoke('home/series/one',
                array('subdomain' => $site, 'alias_menu' => $v->alias));
            $ar['lastmod'] = date("Y-m-d");
            $ar['changefreq'] = "monthly";
            $ar['priority'] = "0.8";
            $arr[] = $ar;
            $arr = $this->checkCountArray($arr);
            $where = "serii.id_menu = '{$v->id}'";
            $mserii = $sm->get('Application\Model\MSeriiTable')->joinSerii()->joinBook()->fetchAll(false, false, $where);

            if ($mserii->count() != 0) {
                foreach ($mserii as $v1) {
                    $ar['loc'] = $this->getServiceLocator()->get('ViewHelperManager')->get('url')->
                    __invoke('home/series/one/book', array('subdomain' => $site, 'alias_menu' => $v->alias,
                        'book' => $v1->book_alias));
                    $ar['lastmod'] = date("Y-m-d");
                    $ar['changefreq'] = "never";
                    $ar['priority'] = "0.6";
                    $arr[] = $ar;
                    $arr = $this->checkCountArray($arr);
                    $where = "soder.id_main = {$v1->book_id}";
                    $soder = $sm->get('Application\Model\SoderTable')->fetchAll(false, 'id ASC', $where);
                    if ($soder->count() != 0) {
                        foreach ($soder as $v2) {
                            $ar['loc'] = $this->getServiceLocator()->get('ViewHelperManager')->get('url')->
                            __invoke('home/series/one/book/content', array('subdomain' => $site,
                                'alias_menu' => $v->alias, 'book' => $v1->book_alias, 'content' => $v2->alias));
                            $ar['lastmod'] = date("Y-m-d");
                            $ar['changefreq'] = "never";
                            $ar['priority'] = "0.4";
                            $arr[] = $ar;
                            $arr = $this->checkCountArray($arr);
                        }
                    }
                }
            }


        }

        //авторы
	$where = "alias != ''";
        $authors = $sm->get('Application\Model\MAvtorTable')->fetchAll(false, false, $where);
        foreach ($authors as $k => $v) {

            $ar['loc'] = $this->getServiceLocator()->get('ViewHelperManager')->get('url')->__invoke('home/authors/one',
                array('subdomain' => $site, 'alias_menu' => $v->alias));
            $ar['lastmod'] = date("Y-m-d");
            $ar['changefreq'] = "monthly";
            $ar['priority'] = "1";
            $arr[] = $ar;
            $arr = $this->checkCountArray($arr);
            $where = "avtor.id_menu = '{$v->id}'";
            $avtor = $sm->get('Application\Model\MAvtorTable')->joinAvtor()->joinBook()->fetchAll(false, false, $where);

            if ($avtor->count() != 0) {
                foreach ($avtor as $v1) {
                    $ar['loc'] = $this->getServiceLocator()->get('ViewHelperManager')->get('url')->
                    __invoke('home/authors/one/book', array('subdomain' => $site, 'alias_menu' => $v->alias,
                        'book' => $v1->book_alias));
                    $ar['lastmod'] = date("Y-m-d");
                    $ar['changefreq'] = "never";
                    $ar['priority'] = "0.8";
                    $arr[] = $ar;
                    $arr = $this->checkCountArray($arr);
                    $where = "soder.id_main = {$v1->book_id}";
                    $soder = $sm->get('Application\Model\SoderTable')->fetchAll(false, 'id ASC', $where);
                    if ($soder->count() != 0) {
                        foreach ($soder as $v2) {
                            $ar['loc'] = $this->getServiceLocator()->get('ViewHelperManager')->get('url')->
                            __invoke('home/authors/one/book/content', array('subdomain' => $site,
                                'alias_menu' => $v->alias, 'book' => $v1->book_alias, 'content' => $v2->alias));
                            $ar['lastmod'] = date("Y-m-d");
                            $ar['changefreq'] = "never";
                            $ar['priority'] = "0.6";
                            $arr[] = $ar;
                            $arr = $this->checkCountArray($arr);
                        }
                    }
                }
            }

        }

        //жанры
        $order = 'book.id ASC';
        $where = 'book.vis = 1';
        $book = $sm->get('Application\Model\BookTable')
            ->joinZhanr()
            ->joinMZhanr()
            ->joinMZhanrParent()
            ->fetchAll(false, $order, $where);


        foreach ($book as $k => $v) {
            $ar['loc'] = $this->getServiceLocator()->get('ViewHelperManager')->get('url')->__invoke('home/genre/one/book', array('subdomain' => $site, 'alias_menu' => $v->n_alias_menu, 's' => $v->n_s, 'book' => $v->alias));
            $ar['lastmod'] = date("Y-m-d");
            $ar['changefreq'] = "monthly";
            $ar['priority'] = "1";
            $arr[] = $ar;
            $arr = $this->checkCountArray($arr);
            $where = "soder.id_main = {$v->id}";
            $soder = $sm->get('Application\Model\SoderTable')->fetchAll(false, 'id ASC', $where);
            if ($soder->count() != 0) {
                foreach ($soder as $v1) {
                    $ar['loc'] = $this->getServiceLocator()->get('ViewHelperManager')->get('url')->__invoke('home/genre/one/book/content', array('subdomain' => $site, 'alias_menu' => $v->n_alias_menu, 's' => $v->n_s, 'book' => $v->alias, 'content' => $v1->alias));
                    $ar['lastmod'] = date("Y-m-d");
                    $ar['changefreq'] = "never";
                    $ar['priority'] = "0.8";
                    $arr[] = $ar;
                    $arr = $this->checkCountArray($arr);
                }
            }
            $where = "text_dop.id_main = {$v->id}";
            $text = $sm->get('Application\Model\TextDopTable')->fetchAll(false, 'id ASC', $where);
            if ($text->count() != 0) {
                for ($i = 1; $i <= $text->count(); $i++) {
                    $ar['loc'] = $this->getServiceLocator()->get('ViewHelperManager')->get('url')->__invoke('home/genre/one/book/read', array('subdomain' => $site, 'alias_menu' => $v->n_alias_menu, 's' => $v->n_s, 'book' => $v->alias, 'page_str' => $i));
                    $ar['lastmod'] = date("Y-m-d");
                    $ar['changefreq'] = "never";
                    $ar['priority'] = "0.8";
                    $arr[] = $ar;
                    $arr = $this->checkCountArray($arr);
                }
            }
        }
        $si = '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $time = date('Y-m-d');
        foreach (glob("*.xml") as $filename) {
            $si .= "<sitemap><loc>https://www.booklot.ru/sitemap/$filename</loc><lastmod>$time</lastmod></sitemap>";
        }
        $si .= '</sitemapindex>';
        $r = fopen("sitemap.xml", "w");
        fwrite($r, $si);
    }

    public function insertFileSitemap($arr)
    {
        $time = time();
        $time = date('Y-m-d', $time);
        $t = '';
        foreach ($arr as $k => $v) {


            $t .= "<url><loc>{$v['loc']}</loc><lastmod>{$v['lastmod']}</lastmod><priority>{$v['priority']}</priority><changefreq>{$v['changefreq']}</changefreq></url>";
        }

        $e = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $e .= $t;
        $e .= '</urlset>';
        $r = fopen("sitemap" . $this->index . ".xml", "w");
        fwrite($r, $e);

    }

    public static function GetImageFromUrl($link)

    {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_POST, 0);

        curl_setopt($ch, CURLOPT_URL, $link);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);

        if ($result === false) {
            return false;
        } else {
            return $result;
        }
        curl_close($ch);
    }

    public static function foto_loc1($foto_all, $width, $dir, $name, $ret = false)
    {
        ini_set('gd.jpeg_ignore_warning', true);
        if (!is_array($foto_all)) {
            $foto_all = array($foto_all);
        }

        foreach ($foto_all as $foto_key) {
            $logo = "public/templates/img/logo.png";
            $size = getimagesize($foto_key);
            if (isset($size) and !empty($size)) {
                $size_log = getimagesize($logo);

                // $xy=$size[0]/$size[1];
                $razWidth = $size[0] / $width;
                $width = $width;
                $height = $size[1] / $razWidth;
                $razWidth_logo = $size_log[0] / $size_log[1];
                $width_logo = $width / 3;
                $height_logo = $width_logo / $razWidth_logo;
                $source_log = imagecreatefrompng($logo);

                switch ($size[2]) {
                    case 1:
                        $source = imagecreatefromgif($foto_key);
                        break;
                    case 2:
                        $source = imagecreatefromjpeg($foto_key);
                        break;
                    case 3:
                        $source = imagecreatefrompng($foto_key);
                        break;
                    default:
                        return false;

                }

                //Создаем подлошку
                $substrate = imagecreatetruecolor($width, $height);
                imagecopyresized($substrate, $source, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
                imagecopyresized($substrate, $source_log, $width - $width_logo, $height - $height_logo, 0, 0, $width_logo, $height_logo, $size_log[0], $size_log[1]);
                imagejpeg($substrate, $dir . $name, 100);
                imagedestroy($substrate);
                imagedestroy($source);
                imagedestroy($source_log);
                if ($ret) {
                    return $name;
                }
            } else {
                continue;
            }
        }

    }

    public static function saveImg($srcLitmir = false)
    {
        ini_set('max_execution_time', 9999999);
        if ($srcLitmir == false) {
            return false;
        }
        if (is_array($srcLitmir)) {
            return false;
        }
        $dir = "/var/www/booklot2.ru/www/templates/newimg/";
        $type = explode(".", $srcLitmir);
        $type = end($type);
        $name = md5($srcLitmir);
        $nameType = $name . '.' . $type;
        $new_src = $dir . "original/" . $nameType;
        $c = mb_strlen($type, "UTF-8");
        $r = self::GetImageFromUrl($srcLitmir);
        if (!$r) return;
        if ($c > 5) {
            $nameType = $name . ".jpg";
            $new_src = $dir . "original/" . $nameType;
            file_put_contents($new_src, $r);
        } else {
            file_put_contents($new_src, $r);
        }

        self::foto_loc1($new_src, '170', $_SERVER['DOCUMENT_ROOT'] . '/templates/newimg/small/', $nameType);
        self::foto_loc1($new_src, '300', $_SERVER['DOCUMENT_ROOT'] . '/templates/newimg/full/', $nameType, true);
        return $nameType;

    }

    public static function img_rep($matches)
    {

        $imgtag = $matches[0];
        if (strripos($imgtag, 'booklot.ru')) return $imgtag;
        preg_match_all("/src[\s]*=[\s]*(\"|\')(.*)(\"|\')/isU", $imgtag, $r);

        self::$text .= " есть фото litmir.net";
        if (isset($r[2][0]) and !empty($r[2][0])) {
            if (!mb_stristr($r[2][0], 'http')) {
                $r[2][0] = $site_url . $r[2][0];
            };
            $srcLitmir = $r[2][0];

            $srcLitmir = preg_replace('/litmir.net/isU', 'litlife.club', $srcLitmir);
            $srcLitmir = preg_replace('/www\./isU', '', $srcLitmir);

            $foto = self::saveImg($srcLitmir);
            self::$text .= " name = $srcLitmir ";
            if (empty($foto)) return "<img src = '$srcLitmir' />";
            self::$text .= " change $foto ";
            $imgtag = preg_replace("/src[\s]*=[\s]*(\"|\')(.*)(\"|\')/isU", 'src="http://www.booklot.ru/templates/newimg/original/' . $foto . '"', $imgtag);
            return $imgtag;
        }

    }

    public function imageChangeAction()
    {

        ini_set('display_errors', 1);
        ini_set("memory_limit", "-1");
        set_time_limit(9999999);
        ini_set('max_execution_time', 9999999);
        $sm = $this->getServiceLocator();
        //$where = "text like '%litmir.net%' and id = '3734'";//BookBinary//3734//6181031//1624256

        //6238821

        //for($i = 1689255; $i <= 6240000; $i=$i+5000 ){

        //$i = false;
        $where = "text like '%litmir.net%'";
        //$where = " id = '5002486'";
        //$where = false;
        $book = $sm->get('Application\Model\TextTable')
            ->fetchAll(false, false, $where);


        foreach ($book as $v) {

            if (mb_stristr($v->text, 'litmir.net', 'UTF-8')) {
                //syslog(LOG_INFO,$v->id.' есть');
            } else {
                //syslog(LOG_INFO,$v->id. ' нет');
                continue;
            }

            $date1 = time();

            self::$text = 'id = ' . $v->id . ' ';

            $id = $v->id;
            $text = $v->text;

            //$text = iconv('utf-8','windows-1251',$text);

            $text = preg_replace('/<script.*<\/script>/isU', '', $text);
            $text = preg_replace('/<div[\s]*class="pages_content"[\s]*style="text-align:center;">[\s]*[0-9]*[\s]*<\/div>/isU', '', $text);

            $text = preg_replace('/<div[\s]*class="lts1"[\s]*>[\s]*<div[\s]*class="lts3"[\s]*>.*<\/div>[\s]*<\/div>/isU', '', $text);


            $text = preg_replace_callback("/<img(.*)>/isU", 'self::img_rep', $text);
            $date2 = time();
            $diff = $date2 - $date1;

            $second = $diff - (int)($diff / 60) * 60;
            self::$text .= ' raz = ' . $diff . ' сек.';
            syslog(LOG_INFO, self::$text);
            if (mb_stristr($text, 'array', 'UTF-8')) {
                continue;
            }
            $data = array();
            $data['text'] = $text;
            $where = array();
            $where['id'] = $id;

            $sm->get('Application\Model\TextTable')->save($data, $where);

        }
        //}


        die();
    }
}
