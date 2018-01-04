<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 05.12.17
 * Time: 13:32
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity\MSerii;
use Application\Entity\Serii;
use Application\Entity\Book;
use Zend\View\Model\ViewModel;


class AdminSeriiController extends AbstractActionController
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
            $seriies = $this->params()->fromPost('serii', null);
            $seriies_delete = $this->getEntityManager()->getRepository(
                Serii::class
            )->findBy(['idMain' => $book_id]);
            if ($seriies_delete) {
                foreach ($seriies_delete as $serii_delete) {
                    $em->remove($serii_delete);
                }
            }
            if ($seriies) {
                foreach ($seriies as $serii) {
                    if(empty($serii))continue;
                    $serii_entity = $this->getEntityManager()->getRepository(
                        MSerii::class
                    )->findOneBy(['name' => $serii]);
                    if (!$serii_entity) {
                        $alias = $sm->get('Main')->trans($serii);
                        do {
                            /** @var $findBy \Application\Entity\MSerii */
                            $findBy = $em->getRepository(MSerii::class)
                                ->findOneBy(
                                    ['alias' => $alias]
                                );

                            $count = 0;
                            if ($findBy != 0) {
                                $alias = $alias.'-';
                                $count = 1;
                            };
                        } while ($count != 0);

                        $serii_entity = new MSerii();
                        $serii_entity->setName($serii);
                        $serii_entity->setAlias($alias);
                        $serii_entity->setIdLitmir(0);
                        $em->persist($serii_entity);
                    }
                    $serii = new Serii();
                    $serii->setIdMenu($serii_entity);
                    $serii->setIdMain($book_entity);
                    $em->persist($serii);

                }
            }
            $em->flush();
        }

        $seriies = $this->getEntityManager()
            ->getRepository(Serii::class)
            ->findBy(['idMain' => $book_id]);

        return new ViewModel(
            [
                'seriies' => $seriies,
            ]
        );
    }

}