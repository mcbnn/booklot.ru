<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 05.12.17
 * Time: 13:32
 */

namespace Application\Controller;

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Model\DocumentFb2;
use Application\Entity\FilesParse;
use Application\Form\FilesParseForm;
use Zend\View\Model\ViewModel;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\File\Transfer\Adapter\Http;
use Zend\Validator\File\Extension;

class AdminFbController extends AbstractActionController
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

    /**
     * @return null|\Zend\Http\Response
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function convertAction()
    {
        ini_set('display_errors', true);
        ini_set('max_input_vars', 100);
        ini_set('post_max_size', '500M');
        ini_set('upload_max_filesize', '500M');
        $config = $this->sm->get('Config');
        $id = $this->params()->fromRoute('id', null);
        if(!$id)return null;
        $em = $this->getEntityManager();
        /** @var \Application\Entity\FilesParse $file */
        $file = $em->getRepository(FilesParse::class)->find($id);
        $file_dir =  $config['UPLOAD_DIR'].'newsave/convert/'.$file->getName();
        $doc = new \DOMDocument();
        $doc->strictErrorChecking = true;
        $doc->recover = false;
        $doc->substituteEntities = false;
        $doc->encoding = 'utf-8';
        $load = $doc->load($file_dir, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOBLANKS);
        if (!$load) {
            echo "Ошибка загрузки!";
        }
        $documentFb2 = new DocumentFb2($this->getEntityManager(), $this->sm);
        $documentFb2->file_id = $id;
        set_time_limit(50);
        $messages = $documentFb2->convert($doc);
        $this->flashMessenger()->addMessage($messages->getError());
        return $this->redirect()->toRoute(
            'home/admin-fb', ['action' => 'add']
        );
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $em = $this->getEntityManager();
        $files = $em->getRepository(FilesParse::class)->findBy([], ['fileId' => 'desc'],    20);

        return new ViewModel(
            [
                'files' => $files,
            ]
        );
    }

    /**
     * @return null|\Zend\Http\Response
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id', null);
        if(!$id)return null;
        $em = $this->getEntityManager();
        $fileparse = $em->getRepository(FilesParse::class)->find($id);
        if($fileparse->getBookId()){
            /** @var  $bookFactory \Application\Controller\BookController */
            $bookFactory = $this->sm->get('book');
            $bookFactory->deleteBook($fileparse->getBookId());
        }
        $em->remove($fileparse);
        $em->flush();
        return $this->redirect()->toRoute(
            'home/admin-fb', ['action' => 'add']
        );
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addAction()
    {
        $config = $this->sm->get('Config');
        $em = $this->getEntityManager();
        $book = new FilesParse();
        $form = new FilesParseForm($em);
        $form->setHydrator(
            new DoctrineObject (
                $em, 'Application\Entity\FilesParse'
            )
        );
        $form->bind($book);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $files = $this->params()->fromFiles();
                if($files){
                    foreach($files['file'] as $file) {
                        $filename = preg_replace('/[^0-9а-яА-ЯЁёa-zA-Z\.0-9 ]*/isu', '', $file['name']);
                        $hash = time();
                        $nameFile = $hash.'_'.$this->sm->get('Main')->trans($filename);
                        $upload_dir = $config['UPLOAD_DIR'];
                        $upload_file = $upload_dir.'newsave/convert/'.$nameFile;
                        if (!move_uploaded_file(
                            $file['tmp_name'],
                            $upload_file
                        )
                        ) {
                            $this->flashMessenger()->addMessage(
                                'Проблема с загрузкой файла'
                            );
                        };
                        $files_parse_entity = new FilesParse();
                        $files_parse_entity->setName($nameFile);
                        $files_parse_entity->setType(0);
                        $em->persist($files_parse_entity);
                    }

                    $em->flush();
                }
                return $this->redirect()->toRoute(
                    'home/admin-fb', ['action' => 'add']
                );
            }
        }

        /** @var \Application\Entity\FilesParse $file */
        $files = $em->getRepository(FilesParse::class)->findBy([], ['fileId' => 'desc'],    200);

        return new ViewModel(
            [
                'form' => $form,
                'files' => $files
            ]
        );
    }

}