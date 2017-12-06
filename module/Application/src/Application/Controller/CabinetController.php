<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 05.12.17
 * Time: 13:32
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
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

    protected function getEntityManager()
    {
        if ($this->em == null) {
            $this->em = $this->getServiceLocator()->get(
                'doctrine.entitymanager.orm_default'
            );
        }
        return $this->em;
    }

    public function commentsAction()
    {
        $user = $this->getServiceLocator()->get('AuthService')->getIdentity();
        $page = $this->params()->fromRoute('page', 1);
        $em = $this->getEntityManager();
        /** @var  $repository \Application\Repository\CommentsRepository */
        $repository = $em->getRepository(Comments::class);
        $query = $repository->getCommentsUser($user->id);
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page);
        $vm = new ViewModel(['paginator' => $paginator]);
        return $vm;
    }

    public function editAction()
    {
        $sm = $this->getServiceLocator();
        $user = $this->getServiceLocator()->get('AuthService')->getIdentity();
        $form = new RegForm();
        /** @var  $request \Zend\Http\PhpEnvironment\Request */
        $request = $this->getRequest();
        $fromFile = $this->params()->fromFiles('foto');

        if ($request->isPost()) {
            $foto = $sm->get('Main')->fotoSave($fromFile);
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $arr = $this->params()->fromPost();
                if (!empty($foto)) {
                    $arr['foto'] = $foto;
                }
                $sm->get('Application\Model\BogiTable')->save(
                    $arr,
                    ['id' => $user->id]
                );
                $this->getServiceLocator()->get('AuthService')->getStorage()
                    ->write(
                        $sm->get('Application\Model\BogiTable')->fetchAll(
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