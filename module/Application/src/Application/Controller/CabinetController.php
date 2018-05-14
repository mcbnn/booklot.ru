<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 05.12.17
 * Time: 13:32
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;
use Application\Form\RegForm;
use Application\Entity\Comments;
use Zend\View\Model\ViewModel;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator as ZendPaginator;

class CabinetController  extends AbstractActionController
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

    public function commentsAction()
    {
        $user = $this->sm->get('AuthService')->getIdentity();
        $page = $this->params()->fromRoute('page', 1);
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Repository\CommentsRepository */
        $repository = $em->getRepository(Comments::class);
        $query = $repository->getCommentsUser($user->id);
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(100);
        $paginator->setCurrentPageNumber($page);
        $vm = new ViewModel(
            [
                'paginator' => $paginator
            ]
        );
        return $vm;
    }

    public function editAction()
    {
        $user = $this->sm->get('AuthService')->getIdentity();
        $form = new RegForm();
        /** @var  $request \Zend\Http\PhpEnvironment\Request */
        $request = $this->getRequest();
        $fromFile = $this->params()->fromFiles('foto');
        if ($request->isPost()) {
            $foto = $this->sm->get('Main')->fotoSave($fromFile);
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $arr = $this->params()->fromPost();
                if (!empty($foto)) {
                    $arr['foto'] = $foto;
                }
                $this->sm->get('Application\Model\BogiTable')->save(
                    $arr,
                    ['id' => $user->id]
                );
                $this->sm->get('AuthService')->getStorage()
                    ->write(
                        $this->sm->get('Application\Model\BogiTable')->fetchAll(
                            false,
                            false,
                            ['id' => $user->id]
                        )->current()
                    );
                return $this->redirect()->toRoute(
                    'home/cabinet'
                );
            }
        }
        return [
            'form' => $form,
            'user' => $user,
        ];
    }
}