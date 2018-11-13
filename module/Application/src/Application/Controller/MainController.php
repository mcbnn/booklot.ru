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
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Mime as Mime;
use Zend\Session\Container as Container;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Protocol\Smtp\Auth;

class MainController extends AbstractActionController
{

    public function checkDubleAlias ($alias, $table, $id)
    {
        $sm = $this->getServiceLocator();
        $where = "alias = '$alias' and id != '$id'";
        $table_s = "Application\Model\\".$table."Table";
        $c = $sm->get($table_s)->fetchAll(false, false, $where);
        if (count($c) != 0) {
            $alias = $alias."-";

            return $this->checkDubleAlias($alias, $table, $id);
        }

        return $alias;
    }


    public function fotoSave ($file)
    {
        $name = "";
        if (isset($file['type']) and ($file['type'] == "image/jpeg"
                or $file['type'] == "image/png"
                or $file['type'] == "image/gif"
                or $file['type'] == "image/jpg")
        ) {
            $uploaddir = $_SERVER['DOCUMENT_ROOT'].'/templates/newimg/original/';

            $name_foto = basename($file['name']);
            $arrFoto = explode('.', $name_foto);
            $typeFoto = strtolower(end($arrFoto));
            $name_foto = md5($name_foto.time());
            $name = $name_foto.'.'.$typeFoto;
            $uploadfile = $uploaddir.$name;

            if (!move_uploaded_file($file['tmp_name'], $uploadfile)) {
                echo "Проблема с загрузкой фотографии";
                die();
            }
        };

        return $name;
    }

    public function fotoSize ($foto, $width, $dirname, $return = false)
    {
        $size = getimagesize($foto);
        $razWidth = $size[0] / $width;
        $hieght = $size[1] / $razWidth;
        switch ($size[2]) {
            case '1':
                $source = imagecreatefromgif($foto);
                break;
            case '2':
                $source = imagecreatefromjpeg($foto);
                break;
            case '3':
                $source = imagecreatefrompng($foto);
                break;
            default:
                return false;
        }
        $substrate = imagecreatetruecolor($width, $hieght);
        imagecopyresized(
            $substrate,
            $source,
            0,
            0,
            0,
            0,
            $width,
            $hieght,
            $size[0],
            $size[1]
        );
        imagejpeg($substrate, $dirname, 100);
        imagedestroy($substrate);
        imagedestroy($source);

        if ($return) {
            return $dirname;
        }
    }

    public function trans ($str)
    {
        $str = html_entity_decode($str);
        //$str = iconv('windows-1251','utf-8',$str);

        //$str = preg_replace('/\&quot;/is', '-',$str);
        $str = preg_replace("/[^a-zA-ZА-Яа-я0-9\s]/isu", "", $str);
        //$str = iconv('utf-8','windows-1251',$str);
        //var_dump($str);
        $str = preg_replace('/\s+/isu', '-', $str);
        $tr = array(
            "А"   => "a",
            "Б"   => "b",
            "В"   => "v",
            "Г"   => "g",
            "Д"   => "d",
            "Е"   => "e",
            "Ж"   => "j",
            "З"   => "z",
            "И"   => "i",
            "Й"   => "y",
            "К"   => "k",
            "Л"   => "l",
            "М"   => "m",
            "Н"   => "n",
            "О"   => "o",
            "П"   => "p",
            "Р"   => "r",
            "С"   => "s",
            "Т"   => "t",
            "У"   => "u",
            "Ф"   => "f",
            "Х"   => "h",
            "Ц"   => "ts",
            "Ч"   => "ch",
            "Ш"   => "sh",
            "Щ"   => "sch",
            "Ъ"   => "",
            "Ы"   => "yi",
            "Ь"   => "",
            "Э"   => "e",
            "Ю"   => "yu",
            "Я"   => "ya",
            "а"   => "a",
            "б"   => "b",
            "в"   => "v",
            "г"   => "g",
            "д"   => "d",
            "е"   => "e",
            "ж"   => "j",
            "з"   => "z",
            "и"   => "i",
            "й"   => "y",
            "к"   => "k",
            "л"   => "l",
            "м"   => "m",
            "н"   => "n",
            "о"   => "o",
            "п"   => "p",
            "р"   => "r",
            "с"   => "s",
            "т"   => "t",
            "у"   => "u",
            "ф"   => "f",
            "х"   => "h",
            "ц"   => "ts",
            "ч"   => "ch",
            "ш"   => "sh",
            "щ"   => "sch",
            "ъ"   => "y",
            "ы"   => "yi",
            "ь"   => "",
            "э"   => "e",
            "ю"   => "yu",
            "я"   => "ya",
            " "   => "_",
            "."   => "",
            "/"   => "_",
            ":"   => "_",
            "-"   => "_",
            "·"   => "_",
            "’"   => "_",
            "'"   => "_",
            "&"   => "_",
            "«"   => "",
            "»"   => "",
            ","   => "_",
            "?"   => "",
            "…"   => "",
            "\""  => "",
            "-"   => "-",
            "("   => "-",
            ")"   => "-",
            ";"   => "-",
            "%"   => "-",
            "#"   => "",
            " "   => "_",
            "  "  => "_",
            "   " => "_",
        );

        $strtr = strtr($str, $tr);
        $str = $strtr;
        $str = preg_replace('/_+/isu', '-', $str);
        $str = preg_replace('/-+/isu', '-', $str);
        $str = trim($str, "-");
        $str = strtolower($str);
        //var_dump($str);
        return $str;
    }

    private function local ()
    {
        return array(
            'name'             => 'localhost',
            'host'             => '127.0.0.1',
            'port'             => 25,
            'connection_class' => 'smtp',
        );
    }

    private function gmail ()
    {
        return array(
            'name'              => 'localhost',
            'host'              => 'smtp.gmail.com',
            'port'              => 587,
            'connection_class'  => 'login',
            'connection_config' => array(
                'username' => 'mcbnn123@gmail.com',
                'password' => 'iggmsasstmharljw',
                'ssl'      => 'tls',
            ),
        );
    }

    private function setMimeType ($link)
    {
        $l = explode('.', $link);
        $type = mb_strtolower(end($l));
        $mask['pdf'] = 'application/pdf';
        $mask['jpg'] = 'image/jpeg';
        $mask['jpeg'] = 'image/jpeg';
        $mask['zip'] = 'application/zip';
        $mask['rtf'] = 'application/rtf';
        $mask['docx']
            = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        $mask['doc'] = 'application/msword';
        try {
            return $mask[$type];
        } catch (\Exception $e) {
            return false;
        }
    }

    public function email4 (
        $connect = 'local',
        $title = "",
        $to = "",
        $from = "",
        $html = "",
        $file = ""
    ) {
        $transport = new SmtpTransport();
        $options = new SmtpOptions($this->$connect());
        $body = new MimeMessage();
        $mail = new \Zend\Mail\Message();
        $htmlPart = new MimePart($html);
        $htmlPart->type = "text/html; charset=utf-8";
        $body->addPart($htmlPart);
        if (!empty($file)) {
            foreach ($file as $k => $v) {
                try {
                    ini_set('display_errors', 0);
                    $attachment = new MimePart(
                        fopen($_SERVER['DOCUMENT_ROOT']."/".$v['link'], 'r')
                    );
                    if (!$this->setMimeType($v['link'])) {
                        return false;
                    }
                    $attachment->type = $this->setMimeType($v['link']);
                    $attachment->encoding = Mime::ENCODING_BASE64;
                    $attachment->disposition = Mime::DISPOSITION_ATTACHMENT;
                    $attachment->filename = $v['filename'];
                    $body->addPart($attachment);
                } catch (\Exception $e) {
                    syslog(LOG_ERR, $e->getMessage());
                }

            }
        }

        //$mail->setEncoding("UTF-8");
        $mail->setFrom($from);
        $mail->setSubject($title);
        $mail->setBody($body);
        $transport->setOptions($options);
        if (is_array($to)) {
            foreach ($to as $v) {
                $to = trim($to);
                if (!filter_var($v, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }
                $mail->setTo($v);
                if($mail->isValid()) {
                    $transport->send($mail);
                    return $transport;
                }
            }
        } elseif (filter_var(trim($to), FILTER_VALIDATE_EMAIL)) {
            $mail->setTo(trim($to));
            if($mail->isValid()) {
                $transport->send($mail);
                return $transport;
             }
        }
        var_dump($mail);
        return false;
    }
}