<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 05.12.17
 * Time: 13:32
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Form\ArticlesForm;
use Application\Entity\Articles;
use Application\Entity\MZhanr;
use Zend\View\Model\ViewModel;
use DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator as ZendPaginator;
use Zend\File\Transfer\Adapter\Http;
use Zend\Validator\File\Extension;

class AdminArticlesController extends AbstractActionController
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em = null;

    /**
     * @return array|\Doctrine\ORM\EntityManager|object
     */
    protected function getEntityManager()
    {
        if ($this->em == null) {
            $this->em = $this->getServiceLocator()->get(
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
        $page = $this->params()->fromRoute('page', 1);
        $em = $this->getEntityManager();
        $get = $this->params()->fromQuery();
        /** @var  $repository \Application\Repository\ArticlesRepository */
        $repository = $em->getRepository(Articles::class);
        $query = $repository->getArticlesAll($get);
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(100);
        $paginator->setCurrentPageNumber($page);

        $menu = $em->getRepository(MZhanr::class)->findBy(['id' => 751]);

        $vm = new ViewModel(
            [
                'paginator' => $paginator,
                'get'       => $get,
                'menu'      => $menu,
            ]
        );

        return $vm;
    }

    /**
     * @param string $type
     *
     * @return \Zend\Http\Response|ViewModel
     */
    protected function addAction($type = 'add')
    {
        $sm = $this->getServiceLocator();
        $em = $this->getEntityManager();

        $articles = new Articles();
        $id = $this->params()->fromRoute('id', false);
        if ($id) {
            $articles = $this->getEntityManager()->getRepository(
                Articles::class
            )->find($id);
        }

        $form = new ArticlesForm($this->getEntityManager());
        $form->setHydrator(
            new DoctrineEntity(
                $this->getEntityManager(), 'Application\Entity\Articles'
            )
        );
        $form->bind($articles);
        /** @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $this->getRequest();

        if ($request->isPost()) {

            $form->setData($request->getPost());
            if ($form->isValid()) {


                $adapter = new Http();

                $adapter->setValidators(
                    [
                        new Extension(
                            [
                                'extension' => ['jpg', 'jpeg', 'png', 'gif'],
                            ]
                        ),
                    ]
                );

                $filename = $adapter->getFilename();
                if($filename != null) {
                    $filename = basename($filename);

                    $hash = $adapter->getHash();
                    $nameFile = $hash.$filename;
                    $adapter->addFilter(
                        'File\Rename',
                        [
                            'target'    => 'public/img/upload/'.$nameFile,
                            'overwrite' => true
                        ]
                    );
                    if (!$adapter->receive()) {
                        echo implode("", $adapter->getMessages());
                    }
                    $articles->setFoto($nameFile);
                }
                $alias = $sm->get('Main')->trans($request->getPost('title'));
                if ($type == 'add') {
                    do {
                        /** @var $findBy \Application\Entity\Articles */
                        $findBy = $em->getRepository(Articles::class)
                            ->findOneBy(
                                ['alias' => $alias]
                            );
                        $count = 0;
                        if ($findBy != 0) {
                            $alias = $alias.'-';
                            $count = 1;
                        };
                    } while ($count != 0);
                }
                $articles->setDatetime(new \Datetime());
                /** @var $menu \Application\Entity\MZhanr */
                $menu = $em->getRepository(MZhanr::class)->find(
                    $request->getPost('menu_id')
                );
                $articles->setMenu($menu);
                $articles->setAlias($alias);
                $em->persist($articles);
                $em->flush();
                return $this->redirect()->toRoute('home/admin-articles');
            }
        }
        return new ViewModel(
            [
                'form' => $form,
                'article' => $articles,
            ]
        );
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    protected function editAction()
    {
        return $this->addAction();
    }

    /**
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        $id = $this->params('id');
        $em = $this->getEntityManager();
        $article = $em->getRepository(Articles::class)->find($id);
        if ($article) {
            $em = $this->getEntityManager();
            $em->remove($article);
            $em->flush();
        }
        return $this->redirect()->toRoute('home/admin-articles');
    }
}