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
use Application\Entity\Book;
use Application\Entity\Bogi;
use Zend\View\Model\ViewModel;

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
     * @return void|ViewModel
     */
    public function indexAction()
    {
        ini_set('display_errors', true);
        ini_set('max_execution_time', 100000);
        $em = $this->getEntityManager();
        $type = $this->params()->fromRoute('type');
        /** @var  $repository \Application\Repository\BookRepository */
        $repository = $em->getRepository(Book::class);
        $books = $repository->getPopularBooks(10);
        if(count($books) == 0)return;
        $viewRender = $this->sm->get('ViewRenderer');
        $config = $this->sm->get('Config');

        $vm = new ViewModel(['books' => $books, 'url' => $config['BASE_URL']]);
        $vm->setTemplate('application/mail/index.phtml');
        $vm->setTerminal(true);
        $html = $viewRender->render($vm);
        $mainController = new MainController();
        $title = "Популярные книги на boooklot.ru";
        $from = "mcbnn123@gmail.com";
        $bogi = $em->getRepository(Bogi::class)->findBy(['vis' => 1]);
        foreach($bogi as $item){
            $to = $item->getEmail();
            $mainController->email4('gmail', $title, $to, $from, $html);
        }
        die();
    }

}