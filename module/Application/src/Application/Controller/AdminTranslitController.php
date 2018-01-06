<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 05.12.17
 * Time: 13:32
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity\MTranslit;
use Application\Entity\Translit;
use Application\Entity\Book;
use Zend\View\Model\ViewModel;

class AdminTranslitController extends AbstractActionController
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
        $translit = $this->params()->fromPost('translit', null);
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
            $translit_delete = $this->getEntityManager()->getRepository(
                Translit::class
            )->findBy(['idMain' => $book_id]);
            if ($translit_delete) {
                foreach ($translit_delete as $translit_delete) {
                    $em->remove($translit_delete);
                }
            }
            if ($translit) {
                foreach ($translit as $translit) {
                    if(empty($translit))continue;
                    $translit_entity = $this->getEntityManager()->getRepository(
                        MTranslit::class
                    )->findOneBy(['name' => $translit]);
                    if (!$translit_entity) {
                        $alias = $sm->get('Main')->trans($translit);
                        do {
                            /** @var $findBy \Application\Entity\MTranslit */
                            $findBy = $em->getRepository(MTranslit::class)
                                ->findOneBy(
                                    ['alias' => $alias]
                                );

                            $count = 0;
                            if ($findBy != 0) {
                                $alias = $alias.'-';
                                $count = 1;
                            };
                        } while ($count != 0);

                        $translit_entity = new MTranslit();
                        $translit_entity->setName($translit);
                        $translit_entity->setAlias($alias);
                        $translit_entity->setIdLitmir(0);
                        $em->persist($translit_entity);
                    }
                    $translit = new Translit();
                    $translit->setIdMenu($translit_entity);
                    $translit->setIdMain($book_entity);
                    $em->persist($translit);
                }
            }
            $em->flush();
        }

        $translit = $this->getEntityManager()
            ->getRepository(Translit::class)
            ->findBy(['idMain' => $book_id]);

        return new ViewModel(
            [
                'translits' => $translit,
            ]
        );
    }

}