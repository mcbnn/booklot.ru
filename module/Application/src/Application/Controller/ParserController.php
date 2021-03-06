<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Curl\Curl;
use Sunra\PhpSimple\HtmlDomParser;
use Application\Entity\Book;
use Application\Entity\MZhanr;

class ParserController
{

    public $sm;
    public $domain = "https://litlife.club";
    public $dir = "/var/www/booklot2.ru/www/templates/newimg/"; //для сайта поменять на  /var/www/booklot2.ru/www/templates/newimg/

    /**
     * @var
     */
    protected $em = null;

    /**
     * @return array|null|object
     */

    public function commentParser($sm, $em)
    {
        ini_set('max_execution_time', 100000);
        /** @var  $sm */
        $this->sm = $sm;
        $this->em = $em;

        $books = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            'id_book_litmir is not null and id_book_litmir != 0 and vis = 1'
        );

        foreach ($books as $k => $book) {
            $id_book_litmir = $book['id_book_litmir'];
            $book_id = $book['id'];
            $href = "/bd/?b=".$id_book_litmir;
            $this->commentsGetContent($href, $id_book_litmir, $book_id);
        }

    }

    public function commentsGetContent($href = false, $id_litmir = false, $book_id = false)
    {
        /** @var  $sm */
        $sm = $this->sm;
        if (!stristr($href, 'http')) {
            $href = $this->domain.$href;
        }
        /** @var  $content \Curl\Curl */
        $content = $this->curl($href);
        if (!$content) {
            return false;
        }
        /** @var  $dom \simplehtmldom_1_5\simple_html_dom */
        $dom = HtmlDomParser::str_get_html($content->response);
        $c = $dom->find("div[jq=Comment]");
        if (count($c) == 0) {
            return;
        }
        foreach ($c as $v) {
            $arrCommentFaik = array();
            $arrCommentFaik['id_book_litmir'] = $id_litmir;
            $arrCommentFaik['book_id'] = $book_id;
            if (count($v->find('div.BBHtmlCodeInner')) == 0) {
                return;
            }
            $text = $v->find('div.BBHtmlCodeInner')[0]->outertext();
            $text = trim(
                strip_tags($text, '<div><p><span><small><font><b><em><i><img>')
            );
            $text = preg_replace_callback(
                '/<img.*src="(.*)".*>/isU',
                function ($matches) {
                    $url_foto_sites = $matches[1];
                    if (!stristr($url_foto_sites, 'litlife.club')) {
                        $url_foto_sites = $this->domain.$url_foto_sites;
                    }
                    $foto_file = $this->curl($url_foto_sites);
                    if (!$foto_file) {
                        return false;
                    }
                    $dir = '/var/www/booklot2.ru/www/templates/newimg/';
                    $type = explode(".", $url_foto_sites);
                    $type = end($type);
                    $name = md5($url_foto_sites);
                    $nameType = $name.'.'.$type;
                    $new_src = $dir."original/".$nameType;
                    file_put_contents($new_src, $foto_file->response);

                    return '<img src = "/templates/newimg/original/'
                        .$nameType
                        .'" >';
                },
                $text
            );
            $arrCommentFaik['text'] = $text;

            if (count($v->find("span.lt99")) == 0) {
                continue;
            }
            $arrCommentFaik['user'] = trim($v->find("span.lt99")[0]->text());

            if (count($v->find('.lt98')) != 0) {
                $id_user_href = $v->find('.lt98')[0]->getAttribute('href');
                preg_match('/\/p\/\?u\=([0-9]*)$/isU', $id_user_href, $id);
                $arrCommentFaik['id_user'] = $id[1];
            } else {
                $arrCommentFaik['id_user'] = 0;
            }

            if (count($v->find('.cm33 img')) != 0) {
                $src = $v->find('.cm33 img')[0]->getAttribute('src');
                $url_foto_sites = $src;
                if (stristr($src, '//litlife.club')) {
                    $url_foto_sites = str_replace(
                        '//litlife.club',
                        $this->domain,
                        $src
                    );
                } elseif (!stristr($src, 'http')) {
                    $url_foto_sites = $this->domain.$src;
                }
                $foto_file = $this->curl($url_foto_sites);
                if (!$foto_file) {
                    return;
                }
                $dir = '/var/www/booklot2.ru/www/templates/newimg/';
                $type = explode(".", $url_foto_sites);
                $type = end($type);
                $name = md5($src);
                $nameType = $name.'.'.$type;
                $new_src = $dir."original/".$nameType;
                file_put_contents($new_src, $foto_file->response);
                $arrCommentFaik['foto'] = $nameType;
            } else {
                $arrCommentFaik['foto'] = "nofoto.jpg";
            }
            $check_comments = $sm->get('Application\Model\CommentsFaikTable')
                ->fetchAll(
                    false,
                    false,
                    [
                        'id_book_litmir' => $id_litmir,
                        'id_user'        => $arrCommentFaik['id_user'],
                    ]
                );

            if ($check_comments->count() == 0) {
                $sm->get('Application\Model\CommentsFaikTable')->save(
                    $arrCommentFaik
                );
            }
        }


    }

    public function parser($sm, $em)
    {
        $this->sm = $sm;
        $this->em = $em;
        for ($m = 82; $m >= 1; $m--) {
            syslog(
                LOG_INFO,
                json_encode(
                    [
                        'elem' => $m,
                    ]
                )
            );
            echo $m.'--';
            $url = $this->domain.'/bs?rs=1%7C0&hc=on&order=date_down&p='.$m;
            $content = $this->curl($url);
            if (!$content) {
                return false;
            }
            $dom = HtmlDomParser::str_get_html($content->response);
            if (count($dom->find('a.lt24')) == 0) {
                continue;
            }
            $a = $dom->find('a.lt24');

            foreach ($a as $v) {
                $this->getOneBook($v->getAttribute('href'));
                //if($this->getOneBook($v->getAttribute('href')))return true;
            }
        }

        return true;

    }

    public function getOneBook($href)
    {
        /** @var  $sm */
        $sm = $this->sm;
        /** @var  $content \Curl\Curl */
        if (!stristr($href, 'http')) {
            $href = $this->domain.$href;
        }
        $content = $this->curl($href);
        if (!$content) {
            return false;
        }
        /** @var  $dom \simplehtmldom_1_5\simple_html_dom */

        $dom = HtmlDomParser::str_get_html($content->response);
        if (count($dom->find('h1')) == 0) {
            return false;
        }
        $name_list = $dom->find('h1')[0]->text();

        $check_book = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            ['name' => $name_list]
        );
        if (count($check_book) != 0 or strstr($name_list, '18+')) {
            return false;
        }
        preg_match('/\/bd\/\?b\=([0-9]*)$/isU', $href, $id);
        $id_book_litmir = $id[1];
        $arrBook = array();
        if (count($dom->find('h1.lt35')) == 0) {
            return false;
        }
        $arrBook['name'] = $dom->find('h1.lt35')[0]->text();
        $check_book = $sm->get('Application\Model\BookTable')->fetchAll(
            false,
            false,
            ['name' => $arrBook['name']]
        );
        if (count($check_book) != 0 or strstr($arrBook['name'], '18+')) {
            return false;
        }

        $alias = $sm->get('Main')->trans($arrBook['name']);
        $alias = $sm->get('Main')->checkDubleAlias($alias, 'book', 0);
        $arrBook['alias'] = $alias;

        $arrBook['text_small'] = "";
        if (count($dom->find("div[jq=BookAnnotationText]")) != 0) {
            $arrBook['text_small'] = trim(
                $dom->find("div[jq=BookAnnotationText]")[0]->text()
            );
        }

        $arrBook['id_book_litmir'] = $id_book_litmir;
        $find = array();
        $arrBook['kol_str'] = 1;

        if (preg_match_all(
            '/Количество[\s]*страниц\:<\/b>(.*)\</isuU',
            $content->response,
            $find
        )
        ) {
            $find = trim($find[1][0]);
            $arrBook['kol_str'] = $find;
        }

        $find = array();
        $arrBook['lang'] = "";
        if (preg_match_all(
            '/\<div[\s]*class\=\"lt36\"\>[\s]*\<b\>Язык[\s]*книги:<\/b>(.*)</isuU',
            $content->response,
            $find
        )
        ) {
            $find = trim($find[1][0]);
            $arrBook['lang'] = $find;
        }

        $arrBook['year'] = 0;
        if (preg_match_all(
            '/Год[\s]*написания[\s]*книги:<\/b>[\s]*\<a[\s]*href\=[a-zA-Z0-9\/\?\=\&\"\']*>(.*)\</isuU',
            $content->response,
            $find
        )
        ) {
            $find = trim($find[1][0]);
            $arrBook['year'] = $find;
        };
        $find = array();
        $arrBook['lang_or'] = "";
        if (preg_match_all(
            '/\<div[\s]*class\=\"lt36\"\>[\s]*\<b\>Язык[\s]*оригинальной[\s]*книги:<\/b>(.*)</isuU',
            $content->response,
            $find
        )
        ) {
            $find = trim($find[1][0]);
            $arrBook['lang_or'] = $find;
        }

        $arrBook['foto'] = 'nofoto.jpg';
        $find = array();
        if (!empty($dom) and count($dom->find(".lt34 img")) != 0) {
            $src = $dom->find(".lt34 img")[0]->getAttribute('src');
            if (stristr($src, 'nocover')) {
                $arrBook['foto'] = 'nofoto.jpg';
            } else {
                $url_foto_sites = $src;
                if (stristr($src, '//litlife.club')) {
                    $src = str_replace('//litlife.club', $this->domain, $src);
                } elseif (!stristr($src, 'http')) {
                    $url_foto_sites = $this->domain.$src;
                }
                $foto_file = $this->curl($url_foto_sites);
                if ($foto_file) {
                    $dir = '/var/www/booklot2.ru/www/templates/newimg/';
                    $type = explode(".", $url_foto_sites);
                    $type = end($type);
                    $name = md5($src);
                    $nameType = $name.'.'.$type;
                    $new_src = $dir."original/".$nameType;
                    file_put_contents($new_src, $foto_file->response);
                    $sm->get('Main')->foto_loc1(
                        $new_src,
                        '170',
                        '/var/www/booklot2.ru/www/templates/newimg/small/',
                        $nameType
                    );
                    $sm->get('Main')->foto_loc1(
                        $new_src,
                        '300',
                        '/var/www/booklot2.ru/www/templates/newimg/full/',
                        $nameType,
                        true
                    );
                    $arrBook['foto'] = $nameType;
                }
            }
        }
        $arrBook['date_add'] = date('Y-m-d H:i:s');
        $arrBook['vis'] = 1;
        $arrBook['sort'] = 0;
        $arrBook['route'] = 'home/genre/one/book';
        $arrBook['visit'] = 0;
        $arrBook['isbn'] = '';
        $arrBook['city'] = '';
        $arrBook['url_partner'] = '';

        $id_book = $sm->get('Application\Model\BookTable')->save(
            $arrBook,
            false,
            true
        );

        //save model book
        //avtor

        $find = array();
        if (preg_match_all(
            '/\<div[\s]*class\=\"lt36\"\>[\s]*\<b\>Автор:<\/b>[\s]*<a[\s]*href="(.*)"[\s]*>(.*)<\/a/isuU',
            $content->response,
            $find
        )
        ) {
            preg_match('/\/a\/\?id\=([0-9]*)$/isU', $find[1][0], $id);
            $id_avtor_litmir = $id[1];
            $find = trim($find[2][0]);
            $m_avtor = $sm->get('Application\Model\MAvtorTable')->fetchAll(
                false,
                false,
                ['name' => $find]
            );
            if ($m_avtor->count() == 0) {
                $arrAvtor = array();
                $arrAvtor['name'] = $find;
                $arrAvtor['id_litmir'] = $id_avtor_litmir;
                $id_avtor = $sm->get('Application\Model\MAvtorTable')->save(
                    $arrAvtor,
                    false,
                    true
                );
                $arrAvtor = array();
                $arrAvtor['alias'] = $id_avtor.'-'.$sm->get('Main')->trans(
                        $find
                    );
                $sm->get('Application\Model\MAvtorTable')->save(
                    $arrAvtor,
                    ['id' => $id_avtor]
                );
            } else {
                $m_avtor = $m_avtor->current();
                $id_avtor = $m_avtor->id;
            }
            $avtor = $sm->get('Application\Model\AvtorTable')->fetchAll(
                false,
                false,
                ['id_main' => $id_book, 'id_menu' => $id_avtor]
            );
            if ($avtor->count() == 0) {
                $arrAvtor = array();
                $arrAvtor['id_main'] = $id_book;
                $arrAvtor['id_menu'] = $id_avtor;
                $sm->get('Application\Model\AvtorTable')->save(
                    $arrAvtor,
                    false
                );
            }
        }

        //serii
        $find = array();
        if (preg_match_all(
            '/\<div[\s]*class\=\"lt36\"\>[\s]*\<b\>Серии:<\/b>[\s]*<a[\s]*href="(.*)"[\s]*>(.*)<\/a/isuU',
            $content->response,
            $find
        )
        ) {
            preg_match(
                '/\/books_in_series\/\?id\=([0-9]*)$/isU',
                $find[1][0],
                $id
            );
            $id_serii_litmir = $id[1];
            $find = trim($find[2][0]);
            $m_serii = $sm->get('Application\Model\MSeriiTable')->fetchAll(
                false,
                false,
                ['name' => $find]
            );
            if ($m_serii->count() == 0) {
                $arrSerii = array();
                $arrSerii['name'] = $find;
                $arrSerii['id_litmir'] = $id_serii_litmir;
                $id_serii = $sm->get('Application\Model\MSeriiTable')->save(
                    $arrSerii,
                    false,
                    true
                );
                $arrSerii = array();
                $arrSerii['alias'] = $id_serii.'-'.$sm->get('Main')->trans(
                        $find
                    );
                $sm->get('Application\Model\MSeriiTable')->save(
                    $arrSerii,
                    ['id' => $id_serii]
                );
            } else {
                $m_serii = $m_serii->current();
                $id_serii = $m_serii->id;
            }
            $serii = $sm->get('Application\Model\SeriiTable')->fetchAll(
                false,
                false,
                ['id_main' => $id_book, 'id_menu' => $id_serii]
            );
            if ($serii->count() == 0) {
                $arrSerii = array();
                $arrSerii['id_main'] = $id_book;
                $arrSerii['id_menu'] = $id_serii;
                $sm->get('Application\Model\SeriiTable')->save(
                    $arrSerii,
                    false
                );
            }
        }

        //zhanr
        $find = array();
        if (preg_match_all(
            '/\<div[\s]*class\=\"lt36\"\>[\s]*\<b\>Жанр:<\/b>(.*)<\/div>/isuU',
            $content->response,
            $find
        )
        ) {
            $find = explode(",", trim(strip_tags($find[1][0])));
            foreach ($find as $v) {
                $find = trim($v);
                $m_zhanr = $sm->get('Application\Model\MZhanrTable')->fetchAll(
                    false,
                    false,
                    ['name' => $find]
                );
                if (count($m_zhanr) == 0) {
                    $arrZhanr = array();
                    $arrZhanr['id_main'] = 500;
                    $arrZhanr['name'] = $find;
                    $arrZhanr['icon'] = null;
                    $arrZhanr['alias'] = $sm->get('Main')->trans($find);
                    $arrZhanr['route'] = 'home/genre/one';
                    $arrZhanr['action'] = null;
                    $arrZhanr['count_book'] = 0;
                    $arrZhanr['vis'] = 1;
                    $arrZhanr['seo_text'] = "";
                    $arrZhanr['keywords'] = "";
                    $arrZhanr['description'] = "";
                    $arrZhanr['see'] = 0;
                    $id_zhanr = $sm->get('Application\Model\MZhanrTable')->save(
                        $arrZhanr,
                        false,
                        true
                    );
                } else {
                    $m_zhanr = $m_zhanr[0];
                    $id_zhanr = $m_zhanr->id;
                }
                $zhanr = $sm->get('Application\Model\ZhanrTable')->fetchAll(
                    false,
                    false,
                    ['id_main' => $id_book, 'id_menu' => $id_zhanr]
                );
                if ($zhanr->count() == 0) {
                    $arrZhanr = array();
                    $arrZhanr['id_main'] = $id_book;
                    $arrZhanr['id_menu'] = $id_zhanr;
                    $sm->get('Application\Model\ZhanrTable')->save(
                        $arrZhanr,
                        false
                    );
                }
                $this->setZhanrToBook($id_zhanr, $id_book);
                break;
            }
        }

        //files
        $files = $this->getDoc($content->response);

        if (!empty($files)) {
            $type_files = "";
            foreach ($files as $k => $v1) {

                if (!$v1) {
                    continue;
                }
                $type_files .= $k.' ,';
                $files_check = $sm->get('Application\Model\BookFilesTable')
                    ->fetchAll(
                        false,
                        false,
                        ['id_book' => $id_book, 'type' => $k]
                    );
                if ($files_check->count() == 0) {

                    $arr_files = array();
                    $arr_files['name'] = $this->saveTxtFb2(
                        $this->domain.$v1,
                        '/templates/newsave/'.$k.'/',
                        $sm->get('Main')->trans($arrBook['name']).'_'.$id_book
                    );
                    if (!$arr_files['name']) {
                        continue;
                    }
                    $dir = '/var/www/booklot2.ru/www/templates/newsave/';
                    $file = $dir.$k.'/'.$arr_files['name'].'.zip';
                    $zip = new \ZipArchive();
                    $zip->open($file);
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $stat = $zip->statIndex($i);
                        $name = basename($stat['name']);
                        $namenew = $sm->get('Main')->trans($arr_files['name'])
                            .'___booklot.ru____.'
                            .$k;

                        $zip->renameName($name, $namenew);
                    }
                    $arr_files['type'] = $k;
                    $arr_files['id_book'] = $id_book;
                    $sm->get('Application\Model\BookFilesTable')->save(
                        $arr_files
                    );
                    $zip->close();
                }
            }
            $type_files = substr($type_files, 0, strlen($type_files) - 2);
            $arrBook = array();
            $arrBook['type_files'] = $type_files;
            $sm->get('Application\Model\BookTable')->save(
                $arrBook,
                ['id' => $id_book]
            );
        }
        //soder
        $find = array();
        $url_soder = $this->domain
            ."/BookShowSectionTitles?BookId="
            .$id_book_litmir;
        $content = $this->curl($url_soder);
        if ($content) {
            $json = json_decode($content->response);

            $find = array();
            if (!empty($json->Content)) {
                //<a class="lt47" href="/br/?b=23100&amp;p=71#">ЧАСТЬ СЕДЬМАЯ</a>
                if (preg_match_all(
                    '/a.*href\=\".*p\=([0-9]*)\#.*>(.*)\<\/a/isUu',
                    $json->Content,
                    $find,
                    PREG_SET_ORDER
                )
                ) {
                    foreach ($find as $v) {
                        $soder = $sm->get('Application\Model\SoderTable')
                            ->fetchAll(
                                false,
                                false,
                                ['id_main' => $id_book, 'num' => (int)$v[1]]
                            );
                        if ($soder->count() == 0) {
                            $arrSoder = array();
                            $arrSoder['id_main'] = $id_book;
                            $arrSoder['name'] = $v[2];
                            $arrSoder['num'] = (empty($v[1])) ? 1 : $v[1];
                            $id_soder = $sm->get('Application\Model\SoderTable')
                                ->save($arrSoder, false, true);
                            $arrSoder = array();
                            $arrSoder['alias'] = $id_soder.'-'.$sm->get('Main')
                                    ->trans($v[2]);
                            $sm->get('Application\Model\SoderTable')->save(
                                $arrSoder,
                                ['id' => $id_soder]
                            );
                        }
                    }
                }
            }
        }

        //text
        $url_text = $this->domain."/br/?b=".$id_book_litmir;
        $content = $this->curl($url_text);
        if ($content) {
            $dom = HtmlDomParser::str_get_html($content->response);
            if (stristr($content->response, 'Unavailable For Legal Reasons')
                or stristr($content->response, 'арше 18 ле')
            ) {
                $arrBook = array();
                $arrBook['vis'] = 0;
                $sm->get('Application\Model\BookTable')->save(
                    $arrBook,
                    ['id' => $id_book]
                );

                return false;
            }
            if (!empty($dom) and count($dom->find("td[jq=button_page] a"))
                != 0
            ) {
                $a = $dom->find("td[jq=button_page] a");
                $max = 1;
                if (!empty($a)) {
                    foreach ($a as $v) {
                        $max = $v->text();
                    }
                }

                for ($i = 1; $i <= $max; $i++) {
                    $text_model = $sm->get('Application\Model\TextTable')
                        ->fetchAll(
                            false,
                            false,
                            ['id_main' => $id_book, 'num' => $i]
                        );
                    if ($text_model->count() == 0) {
                        $url_text_in = $this->domain
                            ."/br/?b="
                            .$id_book_litmir
                            ."&p="
                            .$i;
                        $content = $this->curl($url_text_in);
                        $dom = HtmlDomParser::str_get_html($content->response);
                        if (!empty($dom) and count($dom->find('div.page_text'))
                            != 0
                        ) {
                            $text = $dom->find('div.page_text')[0]->outertext();
                            $text = trim(
                                strip_tags(
                                    $text,
                                    '<div><p><span><small><font><b><em><i><img>'
                                )
                            );
                            $text = preg_replace_callback(
                                '/<img.*src="(.*)".*>/isU',
                                function ($matches) {
                                    $url_foto_sites = $matches[1];
                                    if (!stristr(
                                        $url_foto_sites,
                                        'litlife.club'
                                    )
                                    ) {
                                        $url_foto_sites = $this->domain
                                            .$url_foto_sites;
                                    }
                                    $foto_file = $this->curl($url_foto_sites);
                                    if (!$foto_file) {
                                        return;
                                    }
                                    $dir
                                        = '/var/www/booklot2.ru/www/templates/newimg/';
                                    $type = explode(".", $url_foto_sites);
                                    $type = end($type);
                                    $name = md5($url_foto_sites);
                                    $nameType = $name.'.'.$type;
                                    $new_src = $dir."original/".$nameType;
                                    file_put_contents(
                                        $new_src,
                                        $foto_file->response
                                    );

                                    return '<img src = "/templates/newimg/original/'
                                        .$nameType
                                        .'" >';
                                },
                                $text
                            );
                            $arrBookText = array();
                            $arrBookText['id_main'] = $id_book;
                            $arrBookText['num'] = $i;
                            $arrBookText['text'] = $text;
                            $sm->get('Application\Model\TextTable')->save(
                                $arrBookText
                            );
                        }
                    }
                }
            } else {
                $arrBook = array();
                $arrBook['vis'] = 1;
                $sm->get('Application\Model\BookTable')->save(
                    $arrBook,
                    ['id' => $id_book]
                );

                return false;
            }
        } else {
            $arrBook = array();
            $arrBook['vis'] = 0;
            $sm->get('Application\Model\BookTable')->save(
                $arrBook,
                ['id' => $id_book]
            );

            return false;
        }
        //комменты
        $this->commentsGetContent($href, $id_book_litmir, $id_book);
        return true;
    }

    public function setZhanrToBook($mzanr_id = null, $book_id = null)
    {
        if ($mzanr_id == null or $book_id == null) {
            return false;
        }
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->em;
        $repository = $em->getRepository(MZhanr::class);
        $mzhanr = $repository->find($mzanr_id);
        /** @var \Application\Entity\Book $book */
        $book = $em->getRepository(Book::class)->find($book_id);
        if ($mzhanr->getParent()->getAlias() != 'genre') {
            $book->setNAliasMenu($mzhanr->getAlias());
            $book->setNS($mzhanr->getParent()->getAlias());
            $book->setNameZhanr($mzhanr->getName());
            $book->setMenu($mzhanr);
            $book->setVis(1);
        }
        else{
            $book->setVis(0);
        }
        $em->persist($book);
        $em->flush();
        return true;
    }

    public function saveTxtFb2($url, $dir, $name = false)
    {
        if (!$name) {
            $name = md5(time());
        }
        $dir = '/var/www/booklot2.ru/www'.$dir.$name.'.zip';
        if (copy($url, $dir)) {
            return $name;
        }

        return false;
    }

    public function getDoc($c = false)
    {
        $arr['txt'] = false;
        $arr['fb2'] = false;
        $arr['html'] = false;
        $arr['epub'] = false;
        $arr['rtf'] = false;
        $arr['docx'] = false;
        $arr['odt'] = false;
        $arr['doc'] = false;
        $arr['pdf'] = false;
        $arr['djvu'] = false;
        //$arr['mp3']=false;
        $arr['ogg'] = false;

        if (!empty($c)) {

            $n = '';
            $q = '';
            preg_match_all(
                '/href="(\/BookFileDownloadLink\/\?id=[0-9]*)">Скачать[\s]*txt/isU',
                $c,
                $n
            );
            if (isset($n[1][0]) and !empty($n[1][0])) {
                $arr['txt'] = $n[1][0];
            }
            $n = array();
            preg_match_all(
                '/href="(\/BookFileDownloadLink\/\?id=[0-9]*)">Скачать[\s]*fb2/isU',
                $c,
                $n
            );
            if (isset($n[1][0]) and !empty($n[1][0])) {
                $arr['fb2'] = $n[1][0];
            }

            $n = array();
            preg_match_all(
                '/href="(\/BookFileDownloadLink\/\?id=[0-9]*)">Скачать[\s]*epub/isU',
                $c,
                $n
            );
            if (isset($n[1][0]) and !empty($n[1][0])) {
                $arr['epub'] = $n[1][0];
            }

            $n = array();
            preg_match_all(
                '/href="(\/BookFileDownloadLink\/\?id=[0-9]*)">Скачать[\s]*rtf/isU',
                $c,
                $n
            );
            if (isset($n[1][0]) and !empty($n[1][0])) {
                $arr['rtf'] = $n[1][0];
            }

            $n = array();
            preg_match_all(
                '/href="(\/BookFileDownloadLink\/\?id=[0-9]*)">Скачать[\s]*docx\</isU',
                $c,
                $n
            );
            if (isset($n[1][0]) and !empty($n[1][0])) {
                $arr['docx'] = $n[1][0];
            }

            $n = array();
            preg_match_all(
                '/href="(\/BookFileDownloadLink\/\?id=[0-9]*)">Скачать[\s]*odt/isU',
                $c,
                $n
            );
            if (isset($n[1][0]) and !empty($n[1][0])) {
                $arr['odt'] = $n[1][0];
            }

            $n = array();
            preg_match_all(
                '/href="(\/BookFileDownloadLink\/\?id=[0-9]*)">Скачать[\s]*doc\</isU',
                $c,
                $n
            );
            if (isset($n[1][0]) and !empty($n[1][0])) {
                $arr['doc'] = $n[1][0];
            }

            $n = array();
            preg_match_all(
                '/href="(\/BookFileDownloadLink\/\?id=[0-9]*)">Скачать[\s]*pdf\</isU',
                $c,
                $n
            );
            if (isset($n[1][0]) and !empty($n[1][0])) {
                $arr['pdf'] = $n[1][0];
            }

            $n = array();
            preg_match_all(
                '/href="(\/BookFileDownloadLink\/\?id=[0-9]*)">Скачать[\s]*djvu\</isU',
                $c,
                $n
            );
            if (isset($n[1][0]) and !empty($n[1][0])) {
                $arr['djvu'] = $n[1][0];
            }


            $n = array();
            preg_match_all(
                '/href="(\/BookFileDownloadLink\/\?id=[0-9]*)">Скачать[\s]*ogg\</isU',
                $c,
                $n
            );
            if (isset($n[1][0]) and !empty($n[1][0])) {
                $arr['ogg'] = $n[1][0];
            }


        }

        return $arr;
    }


    /**
     * @param $url
     */
    public function setCookie($url)
    {
        $cookie_file = '/tmp/cookies.txt';
        $curl = new Curl();
        $curl->setCookieJar($cookie_file);
        $curl->get($url);
        $curl->close();
    }

    /**
     * @param      $url
     * @param bool $post
     * @param bool $headers
     * @param bool $read_cookie
     * @param bool $rec_cookies
     *
     * @return Curl
     */
    public function curl(
        $url,
        $post = false,
        $headers = false,
        $read_cookie = false,
        $rec_cookies = false
    ) {
        $curl = new Curl();
        $curl->setOpt(CURLOPT_FOLLOWLOCATION, 1);
        $cookie_file = '/tmp/cookies.txt';
        $curl->setCookieFile($cookie_file);
        $curl->setTimeout(0);
        $curl->setHeaders(
            [
                "User-Agent: Mozilla/"
                .rand(1, 100000000)
                .".0 (Macintosh; Intel Mac OS X 10.6; rv:2.0.1) Gecko/"
                .rand(1, 100000000)
                ." Firefox/"
                .rand(1, 100000000)
                .".0.1",
                "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                "Accept-Language: en-gb,en;q=0.5",
                "Accept-Encoding: gzip, deflate",
                "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7",
                "Keep-Alive: 115",
                "Connection: keep-alive",
                "Referer: https://google.com",
            ]
        );
        if (!$post) {
            $curl->get($url);
        } else {
            $curl->post($url, $post);
        }
        $getInfo = $curl->getInfo();
        $curl->close();


        if ($getInfo['http_code'] == 200) {
            syslog(
                LOG_INFO,
                json_encode(
                    [
                        'http_code' => $getInfo['http_code'],
                        'url'       => $getInfo['url'],
                        'type'      => 'ok',
                    ]
                )
            );
        } else {
            $this->setCookie($this->domain);
            syslog(
                LOG_INFO,
                json_encode(
                    [
                        'http_code' => $getInfo['http_code'],
                        'url'       => $getInfo['url'],
                        'type'      => 'bad',
                    ]
                )
            );

            return false;
        }

        return $curl;
    }

}
