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
use Curl\Curl;
use Sunra\PhpSimple\HtmlDomParser;




class ParserController{

    public $domain = "https://litlife.club";
    public $dir = "/var/www/booklot2.ru/www/templates/newimg/"; //для сайта поменять на  /var/www/booklot2.ru/www/templates/newimg/

    public function commentsGetContent($content = false, $id_litmir = false, $sm){
        $dom = HtmlDomParser::str_get_html($content);
        $c = $dom->find("div[jq=Comment]");
        if(empty($c))return;
        foreach($c as $v){
            $arrCommentFaik = array();
            $arrCommentFaik['id_book_litmir'] = $id_litmir;
            $text = $v->find('div.BBHtmlCodeInner')[0]->outertext();
            $text = trim(strip_tags($text,'<div><p><span><small><font><b><em><i><img>'));
            $text = preg_replace_callback('/<img.*src="(.*)".*>/isU',
                function($matches){
                    $url_foto_sites = $matches[1];
                    if(!stristr($url_foto_sites, 'litlife.club')){
                        $url_foto_sites = $this->domain.$url_foto_sites;
                    }
                    $foto_file = $this->curl($url_foto_sites);

                    $dir = '/var/www/booklot2.ru/www/templates/newimg/';
                    $type = explode(".", $url_foto_sites);
                    $type = end($type);
                    $name = md5($url_foto_sites);
                    $nameType = $name . '.' . $type;
                    $new_src = $dir . "original/" . $nameType;
                    file_put_contents($new_src, $foto_file->response);
                    return '<img src = "/templates/newimg/original/'.$nameType.'" >';
                }
                , $text);
            $arrCommentFaik['text'] = $text;
            if(empty($v->find("span.lt99")))continue;
            $arrCommentFaik['user'] = trim($v->find("span.lt99")[0]->text());
            if(!empty($v->find('.lt98')[0])){
                $id_user_href = $v->find('.lt98')[0]->getAttribute('href');
                preg_match('/\/p\/\?u\=([0-9]*)$/isU', $id_user_href, $id);
                $arrCommentFaik['id_user'] = $id[1];
            }
            else{
                $arrCommentFaik['id_user'] = 0;
            }

            if(!empty($v->find('.cm33 img'))){
                    $src = $v->find('.cm33 img')[0]->getAttribute('src');
                    $url_foto_sites = $src;
                    if(!stristr($src, 'litlife.club')){
                        $url_foto_sites = $this->domain.$src;
                    }
                    $foto_file = $this->curl($url_foto_sites);
                    $dir = '/var/www/booklot2.ru/www/templates/newimg/';
                    $type = explode(".", $url_foto_sites);
                    $type = end($type);
                    $name = md5($src);
                    $nameType = $name . '.' . $type;
                    $new_src = $dir . "original/" . $nameType;
                    file_put_contents($new_src, $foto_file->response);
                    $arrCommentFaik['foto'] = $nameType;
                }
            else{
                    $arrCommentFaik['foto'] = "nofoto.jpg";
            }
            $check_comments =   $sm->get('Application\Model\CommentsFaikTable')->fetchAll(false, false, ['id_book_litmir' => $id_litmir, 'user' => $arrCommentFaik['user']]);
            if($check_comments->count() == 0){
                $sm->get('Application\Model\CommentsFaikTable')->save($arrCommentFaik);
            }
        }


    }

    public function parser($sm){
 
        ini_set('max_execution_time', 10000);
	
	for($m = rand(1,135); $m<=135; $m++){
        $url = $this->domain. '/bs?WrtYearAfter=2017&WrtYearBefore=2017&order=rating_avg_down&p='.$m;
        $content = $this->curl($url);

        $dom = HtmlDomParser::str_get_html($content->response);
        $a = $dom->find('a.lt24');

        foreach($a as $v){
            $href = $this->domain.$v->getAttribute('href');
            $name_list = $this->domain.$v->text();
            $check_book =  $sm->get('Application\Model\BookTable')->fetchAll(false, false, ['name' => $name_list]);
            if($check_book->count() != 0 or strstr($name_list, '18+'))continue;
            preg_match('/\/bd\/\?b\=([0-9]*)$/isU', $href, $id);
            $id_book_litmir = $id[1];
            $content = $this->curl($href);
            $dom = HtmlDomParser::str_get_html($content->response);
            $arrBook = array();
            $arrBook['name'] = $dom->find('h1.lt35')[0]->text();
            $check_book =  $sm->get('Application\Model\BookTable')->fetchAll(false, false, ['name' => $arrBook['name']]);
            if($check_book->count() != 0  or strstr($arrBook['name'], '18+'))continue;
            $alias = $sm->get('Main')->trans($arrBook['name']);
            $alias = $sm->get('Main')->checkDubleAlias($alias, 'book', 0);
            $arrBook['alias'] = $alias;
            $arrBook['text_small'] = $dom->find("div[jq=BookAnnotationText]")[0]->text();
            $arrBook['id_book_litmir'] = $id_book_litmir;
            $find = array();
            $arrBook['kol_str'] = 1;
            if(preg_match_all('/\<div[\s]*class\=\"lt36\"\>[\s]*\<b\>Количество[\s]*страниц:<\/b>(.*)</isuU', $content->response, $find)){
                $find = trim($find[1][0]);
                $arrBook['kol_str'] = $find;
            }
            $find = array();
            $arrBook['lang'] = "";
            if(preg_match_all('/\<div[\s]*class\=\"lt36\"\>[\s]*\<b\>Язык[\s]*книги:<\/b>(.*)</isuU', $content->response, $find)){
                $find = trim($find[1][0]);
                $arrBook['lang'] = $find;
            }
            $arrBook['year'] = "";
            if(preg_match_all('/Год[\s]*написания[\s]*книги:<\/b>[\s]*\<a[\s]*href\=[a-zA-Z0-9\/\?\=\&\"\']*>(.*)\</isuU', $content->response, $find)){
                $find = trim($find[1][0]);
                $arrBook['year'] = $find;
            }
            ;
            $find = array();
            $arrBook['lang_or'] = "";
            if(preg_match_all('/\<div[\s]*class\=\"lt36\"\>[\s]*\<b\>Язык[\s]*оригинальной[\s]*книги:<\/b>(.*)</isuU', $content->response, $find)){
                $find = trim($find[1][0]);
                $arrBook['lang_or'] = $find;
            }
            $find = array();
            $src = $dom->find(".lt34 img")[0]->getAttribute('src');
            if(stristr($src,'nocover')){
                $arrBook['foto'] = 'nofoto.jpg';
            }
            else {
                $url_foto_sites = $src;
                if(!stristr($src, 'litlife.club')){
                    $url_foto_sites = $this->domain.$src;
                }
                $foto_file = $this->curl($url_foto_sites);
                $dir = '/var/www/booklot2.ru/www/templates/newimg/';
                $type = explode(".", $url_foto_sites);
                $type = end($type);
                $name = md5($src);
                $nameType = $name . '.' . $type;
                $new_src = $dir . "original/" . $nameType;
                file_put_contents($new_src, $foto_file->response);
                $sm->get('Main')->foto_loc1($new_src, '170', $_SERVER['DOCUMENT_ROOT'] . '/templates/newimg/small/', $nameType);
                $sm->get('Main')->foto_loc1($new_src, '300', $_SERVER['DOCUMENT_ROOT'] . '/templates/newimg/full/', $nameType, true);
                $arrBook['foto'] = $nameType;
            }
            $arrBook['date_add'] = date('Y-m-d H:i:s');
            $arrBook['vis'] = 1;
            $arrBook['reiting'] = 0;
            $arrBook['sort'] = 0;
            $arrBook['route'] = 'home/genre/one/book';
            $arrBook['visit'] = 0;
            $arrBook['txt'] = '';
            $arrBook['fb2'] = '';
            $arrBook['year_print'] = 0;
            $arrBook['str_litmir'] = 0;
            $arrBook['isbn'] = '';
            $arrBook['city'] = '';
            $arrBook['url_partner'] = '';
            $id_book =  $sm->get('Application\Model\BookTable')->save($arrBook, false, true);

            //save model book
            //avtor
            $find = array();
            if(preg_match_all('/\<div[\s]*class\=\"lt36\"\>[\s]*\<b\>Автор:<\/b>[\s]*<a[\s]*href="(.*)"[\s]*>(.*)<\/a/isuU', $content->response, $find)){
                preg_match('/\/a\/\?id\=([0-9]*)$/isU', $find[1][0], $id);
                $id_avtor_litmir = $id[1];
                $find = trim($find[2][0]);
                $m_avtor = $sm->get('Application\Model\MAvtorTable')->fetchAll(false, false, ['name' => $find]);
                if($m_avtor->count() == 0){
                    $arrAvtor = array();
                    $arrAvtor['name'] = $find;
                    $arrAvtor['id_litmir'] = $id_avtor_litmir;
                    $id_avtor = $sm->get('Application\Model\MAvtorTable')->save($arrAvtor, false, true);
                    $arrAvtor = array();
                    $arrAvtor['alias'] = $id_avtor.'-'.$sm->get('Main')->trans($find);
                    $sm->get('Application\Model\MAvtorTable')->save($arrAvtor, ['id' => $id_avtor]);
                }
                else{
                    $m_avtor = $m_avtor->current();
                    $id_avtor = $m_avtor->id;
                }
                $avtor = $sm->get('Application\Model\AvtorTable')->fetchAll(false, false, ['id_main' => $id_book, 'id_menu' => $id_avtor]);
                if($avtor->count() == 0){
                    $arrAvtor = array();
                    $arrAvtor['id_main'] = $id_book;
                    $arrAvtor['id_menu'] = $id_avtor;
                    $sm->get('Application\Model\AvtorTable')->save($arrAvtor, false);
                }
            }
            //serii
            $find = array();
            if(preg_match_all('/\<div[\s]*class\=\"lt36\"\>[\s]*\<b\>Серии:<\/b>[\s]*<a[\s]*href="(.*)"[\s]*>(.*)<\/a/isuU', $content->response, $find)){
                preg_match('/\/books_in_series\/\?id\=([0-9]*)$/isU', $find[1][0], $id);
                $id_serii_litmir = $id[1];
                $find = trim($find[2][0]);
                $m_serii = $sm->get('Application\Model\MSeriiTable')->fetchAll(false, false, ['name' => $find]);
                if($m_serii->count() == 0){
                    $arrSerii = array();
                    $arrSerii['name'] = $find;
                    $arrSerii['id_litmir'] = $id_serii_litmir;
                    $id_serii = $sm->get('Application\Model\MSeriiTable')->save($arrSerii, false, true);
                    $arrSerii = array();
                    $arrSerii['alias'] = $id_serii.'-'.$sm->get('Main')->trans($find);
                    $sm->get('Application\Model\MSeriiTable')->save($arrSerii, ['id' => $id_serii]);
                }
                else{
                    $m_serii = $m_serii->current();
                    $id_serii = $m_serii->id;
                }
                $serii = $sm->get('Application\Model\SeriiTable')->fetchAll(false, false, ['id_main' => $id_book, 'id_menu' => $id_serii]);
                if($serii->count() == 0){
                    $arrSerii = array();
                    $arrSerii['id_main'] = $id_book;
                    $arrSerii['id_menu'] = $id_serii;
                    $sm->get('Application\Model\SeriiTable')->save($arrSerii, false);
                }
            }

            //zhanr
            $find = array();
            if(preg_match_all('/\<div[\s]*class\=\"lt36\"\>[\s]*\<b\>Жанр:<\/b>(.*)<\/div>/isuU', $content->response, $find)){
                    $find = explode(",",trim(strip_tags($find[1][0])));
                    foreach($find as $v){
                        $find = trim($v);
                        $m_zhanr = $sm->get('Application\Model\MZhanrTable')->fetchAll(false, false, ['name' => $find]);
                        if($m_zhanr->count() == 0){
                            $arrZhanr = array();
                            $arrZhanr['id_main'] = 500;
                            $arrZhanr['name'] = $find;
                            $arrZhanr['eng'] = $sm->get('Main')->trans($find);
                            $arrZhanr['icon'] = NULL;
                            $arrZhanr['alias'] = $arrZhanr['eng'];
                            $arrZhanr['route'] = 'home/genre/one';
                            $arrZhanr['action'] = NULL;
                            $arrZhanr['count_book'] = 0;
                            $arrZhanr['vis'] = 1;
                            $arrZhanr['seo_text'] = "";
                            $arrZhanr['keywords'] = "";
                            $arrZhanr['description'] = "";
                            $id_zhanr = $sm->get('Application\Model\MZhanrTable')->save($arrZhanr, false, true);
                        }
                        else{
                            $m_zhanr = $m_zhanr->current();
                            $id_zhanr = $m_zhanr->id;
                        }
                        $zhanr = $sm->get('Application\Model\ZhanrTable')->fetchAll(false, false, ['id_main' => $id_book, 'id_menu' => $id_zhanr]);
                        if($zhanr->count() == 0){
                            $arrZhanr = array();
                            $arrZhanr['id_main'] = $id_book;
                            $arrZhanr['id_menu'] = $id_zhanr;
                            $sm->get('Application\Model\ZhanrTable')->save($arrZhanr, false);
                    }
                     break;
                    }
            }

            //files
            $files = $this->getDoc($content->response);
            if(!empty($files)){
                foreach($files as $k => $v1){ 
                    if(!$v1)continue;
                    $files_check = $sm->get('Application\Model\BookFilesTable')->fetchAll(false, false, ['id_book' => $id_book, 'type' => $k]);
                    if($files_check->count() == 0) {
                       
                        $arr_files = array();
                        $arr_files['name'] = $this->saveTxtFb2($this->domain . $v1, '/templates/newsave/' . $k . '/', $sm->get('Main')->trans($arrBook['name']) . '_' . $id_book);
                        if (!$arr_files['name']) continue;
			$dir = '/var/www/booklot2.ru/www/templates/newsave/'; 
                        $file = $dir . $k . '/' . $arr_files['name'] . '.zip';
			$zip = new \ZipArchive();
                        $zip->open($file);
                        for ($i = 0; $i < $zip->numFiles; $i++) {
                            $stat = $zip->statIndex($i);
                            $name = basename($stat['name']);
                            $namenew = $sm->get('Main')->trans($arr_files['name']) . '___booklot.ru____.' . $k;
                         
				$zip->renameName($name, $namenew);
                        }
                        $arr_files['type'] = $k;
                        $arr_files['id_book'] = $id_book;
                        $sm->get('Application\Model\BookFilesTable')->save($arr_files);
                        $zip->close();
                    }
                }
            }
            //soder
            $find = array();
            $url_soder = $this->domain."/BookShowSectionTitles?BookId=".$id_book_litmir;
            $content = $this->curl($url_soder);
            $json = json_decode($content->response);
            $find = array();
            if(!empty($json->Content)){
                //<a class="lt47" href="/br/?b=23100&amp;p=71#">ЧАСТЬ СЕДЬМАЯ</a>
                if(preg_match_all('/a.*href\=\".*p\=([0-9]*)\#.*>(.*)\<\/a/isUu', $json->Content, $find, PREG_SET_ORDER)){
                    foreach($find as $v){
                        $soder = $sm->get('Application\Model\SoderTable')->fetchAll(false, false, ['id_main' => $id_book, 'num' => $v[1]]);
                        if($soder->count() == 0){
                            $arrSoder = array();
                            $arrSoder['id_main'] = $id_book;
                            $arrSoder['name'] = $v[2];
                            $arrSoder['num'] = (empty($v[1]))?1:$v[1];
                            $id_soder = $sm->get('Application\Model\SoderTable')->save($arrSoder, false, true);
                            $arrSoder = array();
                            $arrSoder['alias'] = $id_soder.'-'.$sm->get('Main')->trans($v[2]);
                            $sm->get('Application\Model\SoderTable')->save($arrSoder, ['id' => $id_soder]);
                        }
                    }
                }
            }
            //text
            $url_text = $this->domain."/br/?b=".$id_book_litmir;
            $content = $this->curl($url_text);
            $dom = HtmlDomParser::str_get_html($content->response);
            if(stristr($content->response, 'Unavailable For Legal Reasons')
            or
            stristr($content->response, 'арше 18 ле')
            ){
                $arrBook = array();
                $arrBook['vis'] = 0;
                $sm->get('Application\Model\BookTable')->save($arrBook, ['id' => $id_book]);
                continue;
            }
            $a = $dom->find("td[jq=button_page] a");
	    $max = 1;
		if(!empty($a)){
		    foreach ($a as $v){
		        $max = $v->text();
		    }
		}
            for($i = 1; $i <= $max; $i++){
                $text_model = $sm->get('Application\Model\TextTable')->fetchAll(false, false, ['id_main' => $id_book, 'num' => $i]);
                if($text_model->count() == 0){
                    $url_text_in = $this->domain."/br/?b=".$id_book_litmir."&p=".$i;
                    $content = $this->curl($url_text_in);
                    $dom = HtmlDomParser::str_get_html($content->response);


                    $text = $dom->find('div.page_text')[0]->outertext();
                    $text = trim(strip_tags($text,'<div><p><span><small><font><b><em><i><img>'));
                    $text = preg_replace_callback('/<img.*src="(.*)".*>/isU',
                        function($matches){
                            $url_foto_sites = $matches[1];
                            if(!stristr($url_foto_sites, 'litlife.club')){
                                $url_foto_sites = $this->domain.$url_foto_sites;
                            }
                            $foto_file = $this->curl($url_foto_sites);
                            $dir = '/var/www/booklot2.ru/www/templates/newimg/';
                            $type = explode(".", $url_foto_sites);
                            $type = end($type);
                            $name = md5($url_foto_sites);
                            $nameType = $name . '.' . $type;
                            $new_src = $dir . "original/" . $nameType;
                            file_put_contents($new_src, $foto_file->response);
                            return '<img src = "/templates/newimg/original/'.$nameType.'" >';
                        }
                        , $text);
                    $arrBookText = array();
                    $arrBookText['id_main'] = $id_book;
                    $arrBookText['num'] = $i;
                    $arrBookText['text'] = $text;
                    $sm->get('Application\Model\TextTable')->save($arrBookText);
                }
            }
            //комменты
            $this->commentsGetContent($content->response, $id_book, $sm);
            die();
        }
	}

    }


    public function saveTxtFb2($url,$dir, $name = false){
        if(!$name)$name=md5(time());
        $dir=$_SERVER['DOCUMENT_ROOT'].$dir.$name.'.zip';
        if(copy($url,$dir)){
            return $name;
        }
        return false;
    }

    public function getDoc($c = false){
        $arr['txt']=false;
        $arr['fb2']=false;
        $arr['html']=false;
        $arr['epub']=false;
        $arr['rtf']=false;
        $arr['docx']=false;
        $arr['odt']=false;
        $arr['doc']=false;
        $arr['pdf']=false;
        $arr['djvu']=false;
        //$arr['mp3']=false;
        $arr['ogg']=false;

        if(!empty($c)){

            $n='';
            $q='';
            preg_match_all('/href="(\/BookFileDownloadLink\/\?id=[0-9]*)">Скачать[\s]*txt/isU', $c,$n);
            if(isset($n[1][0]) and !empty($n[1][0])){
                $arr['txt']=$n[1][0];
            }
            $n=array();
            preg_match_all('/href="(\/BookFileDownloadLink\/\?id=[0-9]*)">Скачать[\s]*fb2/isU', $c,$n);
            if(isset($n[1][0]) and !empty($n[1][0])){
                $arr['fb2']=$n[1][0];
            }

            $n=array();
            preg_match_all('/href="(\/BookFileDownloadLink\/\?id=[0-9]*)">Скачать[\s]*epub/isU', $c,$n);
            if(isset($n[1][0]) and !empty($n[1][0])){
                $arr['epub']=$n[1][0];
            }

            $n=array();
            preg_match_all('/href="(\/BookFileDownloadLink\/\?id=[0-9]*)">Скачать[\s]*rtf/isU', $c,$n);
            if(isset($n[1][0]) and !empty($n[1][0])){
                $arr['rtf']=$n[1][0];
            }

            $n=array();
            preg_match_all('/href="(\/BookFileDownloadLink\/\?id=[0-9]*)">Скачать[\s]*docx\</isU', $c,$n);
            if(isset($n[1][0]) and !empty($n[1][0])){
                $arr['docx']=$n[1][0];
            }

            $n=array();
            preg_match_all('/href="(\/BookFileDownloadLink\/\?id=[0-9]*)">Скачать[\s]*odt/isU', $c,$n);
            if(isset($n[1][0]) and !empty($n[1][0])){
                $arr['odt']=$n[1][0];
            }

            $n=array();
            preg_match_all('/href="(\/BookFileDownloadLink\/\?id=[0-9]*)">Скачать[\s]*doc\</isU', $c,$n);
            if(isset($n[1][0]) and !empty($n[1][0])){
                $arr['doc']=$n[1][0];
            }

            $n=array();
            preg_match_all('/href="(\/BookFileDownloadLink\/\?id=[0-9]*)">Скачать[\s]*pdf\</isU', $c,$n);
            if(isset($n[1][0]) and !empty($n[1][0])){
                $arr['pdf']=$n[1][0];
            }

            $n=array();
            preg_match_all('/href="(\/BookFileDownloadLink\/\?id=[0-9]*)">Скачать[\s]*djvu\</isU', $c,$n);
            if(isset($n[1][0]) and !empty($n[1][0])){
                $arr['djvu']=$n[1][0];
            }


            $n=array();
            preg_match_all('/href="(\/BookFileDownloadLink\/\?id=[0-9]*)">Скачать[\s]*ogg\</isU', $c,$n);
            if(isset($n[1][0]) and !empty($n[1][0])){
                $arr['ogg']=$n[1][0];
            }


        }

        return $arr;
    }

    public function changeImageText($matches){

        print_r($matches);
        die();

    }


    /**
     * @param $url
     * @param bool $post
     * @param bool $headers
     * @param bool $read_cookie
     * @param bool $rec_cookies
     * @return Curl
     */

    public function curl($url, $post = false, $headers = false, $read_cookie = false, $rec_cookies = false)
    {
//        sleep(rand(4, 14));
        $curl =   new Curl();
//        $curl->setOpt(CURLOPT_HTTPPROXYTUNNEL, 1);
//        $curl->setOpt(CURLOPT_PROXY, $this->CURLOPT_PROXY);
//        $curl->setOpt(CURLOPT_PROXYUSERPWD, $this->CURLOPT_PROXYUSERPWD);
        $curl->setOpt(CURLOPT_FOLLOWLOCATION, 1);
        if ($read_cookie) {
            $curl->setCookieFile($_SERVER['DOCUMENT_ROOT'] . '/cookie.txt');
        }

        if ($headers) {
            $curl->setHeaders($this->getHeaders());
        }

        if ($rec_cookies) {
            $curl->setCookieJar($_SERVER['DOCUMENT_ROOT'] . '/cookie.txt');
        }

        if (!$post) {
            $curl->get($url);
        } else {
            $curl->post($url, $post);
        }
        return $curl;
    }

}
