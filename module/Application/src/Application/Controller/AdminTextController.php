<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 05.12.17
 * Time: 13:32
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity\Text;
use Application\Entity\Book;
use Application\Form\TextForm;
use Zend\View\Model\ViewModel;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

class AdminTextController extends AbstractActionController
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
     * @return null|\Zend\Http\Response
     */
    public function deleteAction()
    {
        $text_id = $this->params()->fromRoute('id', null);
        if(!$text_id)return null;
        /** @var  \Application\Entity\Text $text */
        $em = $this->getEntityManager();
        $text = $em->getRepository(Text::class)->find($text_id);
        if(!$text)return null;
        $book_id = $text->getIdMain()->getId();
        $em->remove($text);
        $em->flush();
        return $this->redirect()->toRoute(
            'home/admin-text',
            [
                'action' => 'edit',
                'id' => $book_id
            ]
        );
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function addAction(){

        $book_id = $this->params()->fromRoute('id', null);
        $em = $this->getEntityManager();
        /** @var  \Application\Entity\Book $book */
        $book = $em->getRepository(Book::class)->find($book_id);
        /** @var  \Application\Entity\Text $text */
        $text = new Text();
        $form = new TextForm($this->getEntityManager());
        $form->setHydrator(
            new DoctrineObject(
                $this->getEntityManager(), 'Application\Entity\Text'
            )
        );
        $form->bind($text);
        /** @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $this->getRequest();
        if ($request->isPost()) {

            $form->setData($request->getPost());
            if ($form->isValid()) {
                $text->setIdMain($book);
                $em->persist($text);
                $em->flush();
                return $this->redirect()->toRoute('home/admin-text',
                    [
                        'action' => 'edit',
                        'id' => $book_id
                    ]
                );
            }
        }
        return new ViewModel(
            [
                'form' => $form,
                'book' => $book
            ]
        );
    }

    public function edittextAction()
    {
        $em = $this->getEntityManager();
        $text_id = $this->params()->fromRoute('id', null);
        /** @var  \Application\Entity\Text $text */
        $text = $em->getRepository(Text::class)->find($text_id);

        /** @var  \Application\Entity\Book $book */
        $book = $em->getRepository(Book::class)->find($text->getIdMain()->getId());
        $form = new TextForm($this->getEntityManager());
        $form->setHydrator(
            new DoctrineObject(
                $this->getEntityManager(), 'Application\Entity\Text'
            )
        );
        $form->bind($text);
        /** @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $this->getRequest();
        if ($request->isPost()) {

            $form->setData($request->getPost());
            if ($form->isValid()) {
                $text->setIdMain($book);
                $em->persist($text);
                $em->flush();
                return $this->redirect()->toRoute('home/admin-text',
                    [
                        'action' => 'edit',
                        'id' => $book->getId()
                    ]
                );
            }
        }
        return new ViewModel(
            [
                'form' => $form,
                'text' => $text
            ]
        );
    }


    /**
     * @return array|ViewModel
     */
    public function editAction()
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
        /** @var  \Application\Entity\Text $text */
        $text = $this->getEntityManager()
            ->getRepository(Text::class)
            ->findBy(['idMain' => $book_id], ['num' => 'asc']);
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
            $em->flush();
        }

        return new ViewModel(
            [
                'text' => $text,
                'book' => $book
            ]
        );
    }

}