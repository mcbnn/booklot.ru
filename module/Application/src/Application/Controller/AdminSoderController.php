<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 05.12.17
 * Time: 13:32
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity\Soder;
use Application\Entity\Book;
use Zend\View\Model\ViewModel;

class AdminSoderController extends AbstractActionController
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

         /** @var  \Application\Entity\Book $book */
        $book = $em->getRepository(Book::class)->find($book_id);
        $soder = $this->getEntityManager()
            ->getRepository(Soder::class)
            ->findBy(['idMain' => $book_id], ['num' => 'asc'])
        ;
        if ($request->isPost()) {
            $soder = $this->params()->fromPost('soder');
            $num = $this->params()->fromPost('num');
            $soder_delete = $this
                ->getEntityManager()
                ->getRepository(Soder::class)
                ->findBy(['idMain' => $book_id]);
            if (count($soder_delete)) {
                foreach ($soder_delete as $soder_delete) {
                    $em->remove($soder_delete);
                }
                $em->flush();
            }
            foreach ($soder as $k => $soder) {

                if (empty($soder)) {
                    continue;
                }
                $alias = $sm->get('Main')->trans($soder);

                do {
                    /** @var $findBy \Application\Entity\Soder */
                    $findBy = $em->getRepository(Soder::class)
                        ->findOneBy(
                            [
                                'alias' => $alias,
                                'idMain' => $book_id
                            ]
                        );
                    $count = 0;
                    if ($findBy != 0) {
                        $alias = $alias.'-';
                        $count = 1;
                    };
                } while ($count != 0);

                $soder_entity = new Soder();
                $soder_entity->setName($soder);
                $soder_entity->setNum($num[$k]);
                $soder_entity->setIdMain($book);
                $soder_entity->setAlias($alias);
                $em->persist($soder_entity);
            }
            $em->flush();
            return $this->redirect()->toRoute(
                'home/admin-soder',
                ['action' => 'index', 'id' => $book->getId()]
            );
        }
        return new ViewModel(
            [
                'soder' => $soder,
            ]
        );
    }

}