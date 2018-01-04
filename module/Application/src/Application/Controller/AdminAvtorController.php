<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 05.12.17
 * Time: 13:32
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity\MAvtor;
use Application\Entity\Avtor;
use Application\Entity\Book;
use Zend\View\Model\ViewModel;


class AdminAvtorController extends AbstractActionController
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
     * @return array|ViewModel
     */
    public function indexAction()
    {
        $book_id = $this->params()->fromRoute('id', null);
        if (!$book_id) {
            return [];
        }
        $em = $this->getEntityManager();
        $sm = $this->getServiceLocator();
        $request = $this->getRequest();
        /** @var  \Application\Entity\Book $book_entity */
        $book_entity = $this->getEntityManager()
            ->getRepository(Book::class)
            ->find($book_id);
        if ($request->isPost()) {
            $avtors = $this->params()->fromPost('avtor', null);

            $avtors_delete = $this->getEntityManager()->getRepository(
                Avtor::class
            )->findBy(['idMain' => $book_id]);
            if ($avtors_delete) {
                foreach ($avtors_delete as $avtor_delete) {
                    $em->remove($avtor_delete);
                }
            }
            if ($avtors) {
                foreach ($avtors as $avtor) {
                    if(empty($avtor))continue;
                    $avtor_entity = $this->getEntityManager()->getRepository(
                        MAvtor::class
                    )->findOneBy(['name' => $avtor]);
                    if (!$avtor_entity) {
                        $alias = $sm->get('Main')->trans($avtor);
                        do {
                            /** @var $findBy \Application\Entity\MAvtor */
                            $findBy = $em->getRepository(MAvtor::class)
                                ->findOneBy(
                                    ['alias' => $alias]
                                );

                            $count = 0;
                            if ($findBy != 0) {
                                $alias = $alias.'-';
                                $count = 1;
                            };
                        } while ($count != 0);

                        $avtor_entity = new MAvtor();
                        $avtor_entity->setName($avtor);
                        $avtor_entity->setAlias($alias);
                        $avtor_entity->setIdLitmir(0);
                        $em->persist($avtor_entity);
                    }
                    $avtor = new Avtor();
                    $avtor->setIdMenu($avtor_entity);
                    $avtor->setIdMain($book_entity);
                    $em->persist($avtor);
                }
            }
            $em->flush();
        }

        $avtors = $this->getEntityManager()
            ->getRepository(Avtor::class)
            ->findBy(['idMain' => $book_id]);

        return new ViewModel(
            [
                'avtors' => $avtors,
            ]
        );
    }

}