<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 05.12.17
 * Time: 13:32
 */

namespace Application\Controller;

use Zend\Http\Response;
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
     * @return Response|ViewModel
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

                if ($filename != null) {
                    $filename = basename($filename);

                    $hash = md5(time()).$adapter->getHash();
                    $nameFile = $hash.$filename;
                    $adapter->addFilter(
                        'File\Rename',
                        [
                            'target' => 'public/templates/newimg/original/'
                                .$nameFile,
                            'overwrite' => true,
                        ]
                    );
                    if (!$adapter->receive()) {
                        echo implode("", $adapter->getMessages());
                    }

                    $dir = '/var/www/booklot2.ru/www/templates/newimg/';
                    $sm->get('Main')->foto_loc1(
                        $dir.'original/'.$nameFile,
                        '170',
                        $dir.'small/',
                        $nameFile
                    );
                    $sm->get('Main')->foto_loc1(
                        $dir.'original/'.$nameFile,
                        '300',
                        $dir.'full/',
                        $nameFile
                    );
                    $book->setFoto($nameFile);
                }

                $alias = $sm->get('Main')->trans($request->getPost('name'));

                if ($type == 'add') {
                    do {
                        /** @var $findBy \Application\Entity\Book */
                        $findBy = $em->getRepository(Book::class)->findOneBy(
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
                    $request->getPost('menu')
                );

                $book->setMenu($menu);
                $book->setAlias($alias);
                $book->setVis(0);
                $book->setNS($menu->getParent()->getAlias());
                $book->setNAliasMenu($menu->getAlias());
                $book->setNameZhanr($menu->getName());
                $em->persist($book);
                $em->flush();

                return $this->redirect()->toRoute(
                    'home/admin-book',
                    ['action' => 'edit', 'id' => $book->getId()]
                );
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
     * @return Response|ViewModel
     */
    public function editAction()
    {
        return $this->addAction();
    }


}