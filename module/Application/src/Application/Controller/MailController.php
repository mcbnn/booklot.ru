<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 05.12.17
 * Time: 13:32
 */

namespace Application\Controller;

use Zend\ServiceManager\ServiceManager;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Form\AdForm;
use Application\Entity\Ad;
use Application\Entity\AdStat;
use Zend\View\Model\ViewModel;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator as ZendPaginator;

class MailController extends AbstractActionController
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em = null;

    /**
     * @var null|ServiceManager
     */
    public $sm = null;

    public function __construct(ServiceManager $servicemanager)
    {
        $this->sm = $servicemanager;
    }

    /**
     * @return array|\Doctrine\ORM\EntityManager|object
     */
    protected function getEntityManager()
    {
        if ($this->em == null) {
            $this->em = $this->sm->get(
                'doctrine.entitymanager.orm_default'
            );
        }
        return $this->em;
    }


    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $em = $this->getEntityManager();
        $type = $this->params()->fromRoute('type');
        $viewRender = $this->sm->get('ViewRenderer');
        $vm = new ViewModel();
        $vm->setTemplate('application/mail/index.phtml');
        $vm->setTerminal(true);
        $html = $viewRender->render($vm);
        $mainController = new MainController();
        $title = "Последние добавленые книги на boooklot.ru";
        $to = "mc_bnn@mail.ru";
        $from = "mcbnn123@gmail.com";
        $mainController->email4('gmail', $title, $to, $from, $html);
        die();
    }

}