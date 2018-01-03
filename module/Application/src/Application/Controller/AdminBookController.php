<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 05.12.17
 * Time: 13:32
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Form\BookForm;
use Application\Entity\Book;
use Application\Entity\MZhanr;
use Zend\View\Model\ViewModel;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

use Zend\File\Transfer\Adapter\Http;
use Zend\Validator\File\Extension;

class AdminBookController extends AbstractActionController
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


    public function indexAction()
    {

    }

    /**
     * @param string $type
     *
     * @return \Zend\Http\Response|ViewModel
     */
    protected function addAction($type = 'add')
    {
        $id = $this->params()->fromRoute('id', null);

        $em = $this->getEntityManager();
        $book = new Book();

        if ($id) {
            $book = $this->getEntityManager()->getRepository(
                Book::class
            )->find($id);
        }

        $form = new BookForm($this->getEntityManager());
        $form->setHydrator(
            new DoctrineObject (
                $this->getEntityManager(), 'Application\Entity\Book'
            )
        );
        $form->bind($book);
        /** @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $this->getRequest();
        $sm = $this->getServiceLocator();
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
                            'target'    => 'public/templates/newimg/original/'.$nameFile,
                            'overwrite' => true,
                        ]
                    );
                    if (!$adapter->receive()) {
                        echo implode("", $adapter->getMessages());
                    }
                    copy ($_SERVER['DOCUMENT_ROOT'].'/templates/newimg/original/'.$nameFile, $_SERVER['DOCUMENT_ROOT'].'/templates/newimg/small/'.$nameFile);
                    copy ($_SERVER['DOCUMENT_ROOT'].'/templates/newimg/original/'.$nameFile, $_SERVER['DOCUMENT_ROOT'].'/templates/newimg/full/'.$nameFile);

                  $book->setFoto($nameFile);
                }

                $alias = $sm->get('Main')->trans($request->getPost('name'));

                if ($type == 'add') {
                    do {
                        /** @var $findBy \Application\Entity\Book */
                        $findBy = $em->getRepository(Book::class)
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
                $book->setDateAdd(new \Datetime());
                /** @var $menu \Application\Entity\MZhanr */
                $menu = $em->getRepository(MZhanr::class)->find(
                    $request->getPost('menuId')
                );

                $book->setMenu($menu);
                $book->setAlias($alias);
                $book->setVis(0);
                $em->persist($book);
                $em->flush();
                return $this->redirect()->toRoute('home/admin-book', ['action' => 'edit', 'id' => $book->getId()]);
            }
        }

        return new ViewModel(
            [
                'form' => $form,
                'book' => $book,
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