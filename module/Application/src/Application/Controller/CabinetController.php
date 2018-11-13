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
use Application\Entity\Bogi;
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
        $em = $this->getEntityManager();
        if ($request->isPost()) {
            var_dump($fromFile);
            die();
            $foto = $this->sm->get('Main')->fotoSave($fromFile);
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $arr = $this->params()->fromPost();
                if (!empty($foto)) {
                    $arr['foto'] = $foto;
                }
                /** @var \Application\Entity\Bogi $user_entity */
                $user_entity = $em->getRepository(Bogi::class)->find($user->id);
                $user_entity->setName($arr['name']);
                $user_entity->setBirth(new \DateTime($arr['birth']));
                $user_entity->setSex($arr['sex']);
                $user_entity->setFoto($arr['foto']);
                $em->persist($user_entity);
                $em->flush();
                $object = (object)[
                    'id' => $user_entity->getId(),
                    'name'  => $user_entity->getName(),
                    'password'  => $user_entity->getPassword(),
                    'email'  => $user_entity->getEmail(),
                    'birth' => $user_entity->getBirth()->format('Y-m-d'),
                    'sex' => $user_entity->getSex(),
                    'foto' => $user_entity->getFoto(),
                    'comments' => $user_entity->getComments(),
                    'datetime_reg' => $user_entity->getDatetimeReg()->format('Y-m-d'),
                    'datetime_log' => $user_entity->getDatetimeLog()->format('Y-m-d'),
                    'my_book' => $user_entity->getMyBook(),
                    'role' => $user_entity->getRole(),
                    'count_status_book'  => $user_entity->getCountStatusBook(),
                ];
                $this->sm->get('AuthService')->getStorage()
                    ->write(
                        $object
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