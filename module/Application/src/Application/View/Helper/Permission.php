<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;
use Doctrine\ORM\EntityManager;
use Zend\Http\PhpEnvironment\Request;

class Permission extends AbstractHelper
{
    /**
     * @var null|AuthenticationService
     */
    public $as = null;

    public $em = null;

    public $request = null;
    /**
     * Button constructor.
     *
     * @param AuthenticationService $AuthService
     */
    public function __construct(AuthenticationService $AuthService, EntityManager $EntityManager,Request $request)
    {
        /** @var \Zend\Authentication\AuthenticationService as */
        $this->as = $AuthService;
        /** @var \Doctrine\ORM\EntityManager em */
        $this->em = $EntityManager;
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
        if($this->request->getHeaders()->get('Cookie')->offsetExists('old')){
            $old = $this->request->getHeaders()->get('Cookie')->offsetGet('old');
        }
        var_dump($old);
        var_dump($permission);
        if($permission == 1 and $old == 0){
            return $this->getView()->render('application/index/permission');
        }
    }
}