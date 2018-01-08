<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 05.12.17
 * Time: 13:32
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Model\DocumentFb2;
use Application\Entity\BookFiles;
use Application\Form\BookFilesForm;
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

    public function convertAction(){

        $id = $this->params()->fromRoute('id', null);
        if(!$id)return;
        $em = $this->getEntityManager();
        $file = $em->getRepository(BookFiles::class)->find($id);
        $dir =  $_SERVER['DOCUMENT_ROOT'].'/templates/newsave/fb2/';
        $dir_convert =  $_SERVER['DOCUMENT_ROOT'].'/templates/newsave/convert/';
        $zip = new \ZipArchive();
        $dir_name = $dir.$file->getName().'.zip';
        $dir_convert_name = $dir_convert.$file->getName().'.fb2';

        try{
            if ($zip->open($dir_name) === TRUE) {
                $zip->extractTo($dir_convert);
                $zip->close();
            }
        }
        catch (\Exception $e){
            echo "Ошибка zip";
        }

        $doc = new \DOMDocument();

        $doc->strictErrorChecking = true;
        $doc->recover = false;
        $doc->substituteEntities = false;
        $doc->encoding = 'utf-8';

        $load = $doc->load($dir_convert_name, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOBLANKS);
        if (!$load) {
            echo "Ошибка загрузки!";
        }

        $documentFb2 = new DocumentFb2($this->getEntityManager());
        $documentFb2->convert($doc);
        return $this->redirect()->toRoute(
            'home/admin-fb'
        );

    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {

        $em = $this->getEntityManager();
        $files = $em->getRepository(BookFiles::class)->findBy(
            ['idBook' => null],
            ['idBookFiles' => 'DESC']
        );
        return new ViewModel(
            [
                'files' => $files,
            ]
        );
    }

    /**
     * @return ViewModel
     */
    public function addAction()
    {
        $em = $this->getEntityManager();
        $book = new BookFiles();
        $form = new BookFilesForm($em);
        $form->setHydrator(
            new DoctrineObject (
                $em, 'Application\Entity\BookFiles'
            )
        );
        $form->bind($book);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $adapter = new Http();
                $adapter->setValidators(
                    [
                        new Extension(
                            [
                                'extension' => [
                                    'fb2',
                                ],
                            ]
                        ),
                    ]
                );
                $filenames = $adapter->getFileInfo();
                foreach ($filenames as $file => $info) {
                    $filename = $info['name'];
                    $exp = explode('.', $filename);
                    $type = end($exp);
                    if (!$adapter->isValid($file)) {
                        echo implode("", $adapter->getMessages());
                        continue;
                    }
                    $zip = new \ZipArchive();
                    $time = time();
                    $zip_name = $time.'.zip';
                    $destination = $_SERVER['DOCUMENT_ROOT']
                        .'/templates/newsave/'
                        .$type
                        .'/'
                        .$zip_name;
                    if ($zip->open($destination, \ZIPARCHIVE::CREATE) !== true) {
                        echo 'Проблема с созданием каталога zip';
                    }
                    $zip->addFile(
                        $info['tmp_name'],
                        $time.'.'.$type
                    );
                    $zip->close();
                    $book_file_entity = new BookFiles();
                    $book_file_entity->setName($time);
                    $book_file_entity->setType($type);
                    $em->persist($book_file_entity);
                }
                $em->flush();
                return $this->redirect()->toRoute(
                    'home/admin-fb'
                );
            }
        }
        return new ViewModel(
            [
                'form' => $form,
            ]
        );
    }

}