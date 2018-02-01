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
    /**
     * Button constructor.
     *
     * @param AuthenticationService $AuthService
     */
    public function __construct(AuthenticationService $AuthService, EntityManager $EntityManager, ServiceManager $ServiceManager)
    {
        /** @var \Zend\Authentication\AuthenticationService as */
        $this->as = $AuthService;
        /** @var \Doctrine\ORM\EntityManager em */
        $this->em = $EntityManager;
        /** @var \Zend\ServiceManager\ServiceManager sm */
        $this->sm = $ServiceManager;
    }


    public function name($name = null)
    {
        $ad = $this->em->getRepository(AdEntity::class)
            ->findOneBy(['name' => $name, 'vis' => 1]);
        if(!$ad)return;
        $request = $this->sm->get('Request')->getRequestUri();
        return $this->getView()->render('application/ad/view',
            [
                'ad' => $ad,
                'request' => $request
            ]
        );
    }
}