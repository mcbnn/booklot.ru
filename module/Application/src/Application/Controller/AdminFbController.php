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
use Application\Entity\MZhanr;

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

    public function addTestAction()
    {
    }

    public function testAction()
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
            $files = $this->params()->fromFiles('file');
            if($files){
                foreach($files as $file){
                    $doc = new \DOMDocument();
                    $doc->strictErrorChecking = true;
                    $doc->recover = false;
                    $doc->substituteEntities = false;

                    $load = $doc->load($file['tmp_name'], LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOBLANKS);
                    if (!$load) {
                        echo "Ошибка загрузки!";
                        die();
                    }
                    $documentFb2 = new DocumentFb2($this->getEntityManager(), $this->sm, $this->params()->fromQuery('validation', null));
                    $messages = $documentFb2->test($doc);
                }
            }
        }
        return new ViewModel(
            [
                'form' => $form
            ]
        );
    }

    /**
     * @return null|\Zend\Http\Response
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function convertAction($id = null, $validation = false, $redirect = true)
    {
        $config = $this->sm->get('Config');
        if(!$id)$id = $this->params()->fromRoute('id', null);
        if (!$id) {
            return null;
        }
        if(!$validation)$validation = $this->params()->fromQuery('validation', null);
        $em = $this->getEntityManager();
        /** @var \Application\Entity\FilesParse $file */
        $file = $em->getRepository(FilesParse::class)->find($id);
        $file_dir = $config['UPLOAD_DIR'].'newsave/convert/'.$file->getName(
            );
        $doc = new \DOMDocument();
        $doc->strictErrorChecking = true;
        $doc->recover = false;
        $doc->substituteEntities = false;
        $doc->encoding = 'utf-8';
        $load = $doc->load(
            $file_dir,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOBLANKS
        );
        if (!$load) {
	        $this->flashMessenger()->addMessage("Ошибка загрузки!");
            echo "Ошибка загрузки!";
            return;
        }

        $documentFb2 = new DocumentFb2(
            $this->getEntityManager(),
            $this->sm,
            $validation
        );
        $documentFb2->file_id = $id;
        set_time_limit(50);
        $messages = $documentFb2->convert($doc);
        $this->flashMessenger()->addMessage($messages->getError());
        if($redirect)
        {
            return $this->redirect()->toRoute(
                'home/admin-fb', ['action' => 'add']
            );
        }
        if(!$messages->getError())return true;
        return false;
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
        ini_set('display_errors', 1);

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
                        if ($file['type'] == 'application/zip') {
                            $zip = new \ZipArchive();
                            $path = $file['tmp_name'];
                            if ($zip->open($path, \ZipArchive::CREATE) != TRUE) {
                                exit("Невозможно открыть\n");
                            }
                            $path_books = $_SERVER['DOCUMENT_ROOT'].'/book_download/book/';
                            if (is_dir($path_books)) {
                                exec('rm -r '.$path_books);
                            }
                            mkdir($path_books, 0777, true);
                            for ($i=0; $i<$zip->numFiles;$i++) {
                                $filename = $zip->getNameIndex($i);
                                $fileinfo = pathinfo($filename);
                                if (strstr($filename, '.fb2')) {
                                    copy("zip://".$path."#".$filename, $path_books.$fileinfo['basename']);
                                }
                            }
                            foreach (scandir($path_books) as $k => $v) {
                                if (strstr($v, '.fb2')) {
                                    $file = [];
                                    $file['name'] = $v;
                                    $file['type'] = 'application/x-fictionbook+xml';
                                    $file['tmp_name'] = $path_books.$v;
                                    $file['error'] = 0;
                                    $file['size'] = 0;
                                    $this->fileSave($file, $request->getPost('validation'));
                                }
                            }
                            $this->flashMessenger()->addMessage(
                                'Загрузка zip архива'
                            );
                            exec('rm -r '.$path_books);
                            break;
                        }
                        $this->fileSave($file, $request->getPost('validation'));
                    }
                }
                return $this->redirect()->toRoute(
                    'home/admin-fb', ['action' => 'add']
                );
            }
        }

        /** @var \Application\Entity\FilesParse $file */
        $files = $em->getRepository(FilesParse::class)->findBy([], ['fileId' => 'desc'],    450);

        return new ViewModel(
            [
                'form' => $form,
                'files' => $files
            ]
        );
    }

    protected function fileSave($file, $validation) {
        $config = $this->sm->get('Config');
        $em = $this->getEntityManager();
        $hash = time();
        $filename = $this->sm->get('Main')->trans($file['name']);
        $filename = preg_replace('/[^0-9а-яА-ЯЁёa-zA-Z\.0-9 ]*/isu', '',$filename);
        $nameFile =  $hash.'_'.$filename;
        $nameFile = substr($nameFile, 0, 50).'.fb2';
        $upload_dir = $config['UPLOAD_DIR'];
        $upload_file = $upload_dir.'newsave/convert/'.$nameFile;
        if (!copy(
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
        if($this->convertAction($files_parse_entity->getFileId(), $validation, false))
        {
            $em->flush();
        }
        $em->clear();
    }

}