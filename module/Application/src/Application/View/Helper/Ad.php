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
        var_dump($reklama);
        if($reklama)return;
        $request = $this->sm->get('Request')->getRequestUri();
        return $this->getView()->render('application/ad/view',
            [
                'ad' => $ad,
                'request' => $request
            ]
        );
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