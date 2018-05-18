<?php
namespace Application\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;
use Doctrine\ORM\EntityManager;

class Permission extends AbstractHelper
{
    /**
     * @var null|AuthenticationService
     */
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

    /**
     * @param null $link
     *
     * @return null
     */
    public function zhanrOld($permission = 0)
    {
        $old = 0;
        $cookie = $this->request->getHeaders()->get('Cookie');
        if($cookie != null and $cookie->offsetExists('old')){
            $old = $this->request->getHeaders()->get('Cookie')->offsetGet('old');
        }

        if($permission == 1 and $old == 0){
            return $this->getView()->render('application/index/permission');
        }
    }
}