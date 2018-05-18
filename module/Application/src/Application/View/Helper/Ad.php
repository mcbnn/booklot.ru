<?php
namespace Application\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;
use Doctrine\ORM\EntityManager;
use Application\Entity\Ad as AdEntity;


class Ad extends AbstractHelper
{
    public $as = null;

    public $em = null;

    public $sm = null;

    public $request = null;
    /**
     * Button constructor.
     *
     * @param AuthenticationService $AuthService
     */
    public function __construct(AuthenticationService $AuthService, EntityManager $EntityManager, ServiceManager $ServiceManager, $request)
    {
        /** @var \Zend\Authentication\AuthenticationService as */
        $this->as = $AuthService;
        /** @var \Doctrine\ORM\EntityManager em */
        $this->em = $EntityManager;
        /** @var \Zend\ServiceManager\ServiceManager sm */
        $this->sm = $ServiceManager;
        /** @var \Zend\Http\PhpEnvironment\Request request */
        $this->request = $request;
    }

    public function iframe($type = 'ad1'){
        return $this->getView()->render('application/ad/'.$type,
            [
            ]
        );
    }

    /**
     * @param null $name
     *
     * @return string|void
     */
    public function name($name = null)
    {
        if($this->block())return;
        $ad = $this->em->getRepository(AdEntity::class)
            ->findOneBy(['name' => $name, 'vis' => 1]);
        if(!$ad)return;
        $reklama = 0;
        $cookie = $this->request->getHeaders()->get('Cookie');
        if($cookie != null and $cookie->offsetExists('reklama')){
            $reklama = $this->request->getHeaders()->get('Cookie')->offsetGet('reklama');
        }
        if($reklama)return;
        $request = $this->sm->get('Request')->getRequestUri();
        return $this->getView()->render('application/ad/view',
            [
                'ad' => $ad,
                'request' => $request
            ]
        );
    }

    public function content($text)
    {
        if($this->block())return;
        $ad = $this->em->getRepository(AdEntity::class)
            ->findOneBy(['name' => 'in_content', 'vis' => 1]);
        if(!$ad)return;
        $reklama = 0;
        $cookie = $this->request->getHeaders()->get('Cookie');
        if($cookie != null and $cookie->offsetExists('reklama')){
            $reklama = $this->request->getHeaders()->get('Cookie')->offsetGet('reklama');
        }
        if($reklama){
            return $text;
        };

        if (!$text) {
            return "<div class = 'alert alert-warning'>Сайт находится в наполнение, по поводу замечаний пишите на почту <a href = 'mailto:mc_bnn@mail.ru?subject=Вопрос по замечанию на сайте'>mc_bnn@mail.ru</a></div>";
        }
        $text = preg_replace("/\<a(.*)\>(.*)\<\/a\>/iU", "$2", $text);
        $txt = '';
        $i = rand(0,2);
        $request = $this->sm->get('Request')->getRequestUri();
        $block = $this->getView()->render('application/ad/view',
            [
                'ad' => $ad,
                'request' => $request
            ]
        );

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
        $text = str_replace($arr, $arr1, $text);

        return $text;

    }

    public function config()
    {
        if($this->block())return;
        return $this->getView()->render('application/ad/config');
    }

    public function block(){
        $arr[] = '/genre/fantastika-i-fentezi/ujasyi-i-mistika/';
        $request = $this->sm->get('Request')->getRequestUri();
        foreach($arr as $v){
            if($request == $v){
                return true;
            }
        }
        return false;
    }
}