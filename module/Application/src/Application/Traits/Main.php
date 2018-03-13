<?php

namespace Application\Traits;

use Application\Entity\MAvtor;
use Application\Entity\MSerii;
use Application\Entity\MTranslit;

trait Main{

    /**
     * @return null
     */
    public function getTranslit()
    {
        $alias_menu = $this->sm->get('Application')->getMvcEvent()->getRouteMatch()->getParam('alias_menu');
        if(!$alias_menu)return null;
        $repository = $this->getEntityManager()->getRepository(MTranslit::class);
        $translit = $repository->findOneBy(['alias' => $alias_menu]);
        if(count($translit) == 1)return $translit;
        return null;
    }

    /**
     * @return null
     */
    public function getAvtor()
    {
        $alias_menu = $this->sm->get('Application')->getMvcEvent()->getRouteMatch()->getParam('alias_menu');
        if(!$alias_menu)return null;
        $repository = $this->getEntityManager()->getRepository(MAvtor::class);
        $avtor = $repository->findOneBy(['alias' => $alias_menu]);
        if(count($avtor) == 1)return $avtor;
        return null;
    }

    /**
     * @return null
     */
    public function getSerii()
    {
        $alias_menu = $this->sm->get('Application')->getMvcEvent()->getRouteMatch()->getParam('alias_menu');
        if(!$alias_menu)return null;
        $repository = $this->getEntityManager()->getRepository(MSerii::class);
        $serii = $repository->findOneBy(['alias' => $alias_menu]);
        if(count($serii) == 1)return $serii;
        return null;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * @param bool $n
     */
    public function noindex($n = true)
    {
        $sm = $this->sm;
        $renderer = $sm->get(
            'Zend\View\Renderer\PhpRenderer'
        );
        if ($n) {
            $renderer->headMeta()->appendName('ROBOTS', 'NOINDEX,FOLLOW');
        } else {
            $renderer->headMeta()->appendName('ROBOTS', 'INDEX,FOLLOW');
        }
    }


    /**
     * @param        $name
     * @param string $title
     * @param string $discription
     * @param string $keywords
     */
    public function seo($name, $title = "", $discription = "", $keywords = "")
    {
        $title = (empty($title)) ? $name : $title;
        $discription = (empty($discription)) ? $title : $discription;
        $keywords = (empty($keywords)) ? $title : $keywords;
        $sm = $this->sm;
        $renderer = $sm->get(
            'Zend\View\Renderer\PhpRenderer'
        );
        $renderer->headTitle($title);
        $renderer->headMeta()->appendName('description', $discription);
        $renderer->headMeta()->appendName('keywords', $keywords);
    }

}