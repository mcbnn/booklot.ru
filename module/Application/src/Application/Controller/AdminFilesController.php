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
use Application\Entity\BookFiles;
use Application\Entity\Book;
use Zend\View\Model\ViewModel;

class AdminFilesController extends AbstractActionController
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
     * @return array|ViewModel
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function indexAction()
    {
        $config = $this->sm->get('Config');
        $book_id = $this->params()->fromRoute('id', null);
        if (!$book_id) {
            return [];
        }
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        /** @var  \Application\Entity\Book $book_entity */
        $book_entity = $this->getEntityManager()
            ->getRepository(Book::class)
            ->find($book_id);
        if ($request->isPost()) {
            $files_delete = $this->getEntityManager()->getRepository(
                BookFiles::class
            )->findBy(['idBook' => $book_id]);
            if ($files_delete) {
                foreach ($files_delete as $file_delete) {
                    $em->remove($file_delete);
                }
            }
            $file_name = $this->params()->fromPost('file_name', null);
            $file_type = $this->params()->fromPost('file_type', null);
            if($file_name){
                foreach($file_name as $k => $f){
                    $book_file_entity = new BookFiles();
                    $book_file_entity->setName($f);
                    $book_file_entity->setIdBook($book_entity);
                    $book_file_entity->setType($file_type[$k]);
                    $em->persist($book_file_entity);
                }
            }

            $files = $this->params()->fromFiles();
            if($files) {
                foreach ($files['file'] as $file) {
                    $filename = $file['name'];
                    $exp = explode('.', $filename);
                    $type = end($exp);
                    $hash = time();
                    $nameFile = $hash.'_'.$filename;
                    $upload_dir = $config['UPLOAD_DIR'];
                    $zip = new \ZipArchive();
                    $zip_name = $book_entity->getAlias().'_'.$book_entity->getId().'.zip';
                    $destination = $upload_dir.'/newsave/'.$type .'/'.$zip_name;
                    if ($zip->open($destination, \ZIPARCHIVE::CREATE) !== true) {
                        $this->flashMessenger()->addMessage('Проблема с созданием zip');
                    }
                    $zip->addFile(
                        $file['tmp_name'],
                        $book_entity->getAlias().'.'.$type
                    );
                    $zip->close();
                    $book_file_entity = new BookFiles();
                    $book_file_entity->setName($book_entity->getAlias().'_'.$book_entity->getId());
                    $book_file_entity->setIdBook($book_entity);
                    $book_file_entity->setType($type);
                    $em->persist($book_file_entity);
                }
            }
            $em->flush();
        }
        $book_files = $this->getEntityManager()
            ->getRepository(BookFiles::class)
            ->findBy(['idBook' => $book_id]);

        return new ViewModel(
            [
                'files' => $book_files,
                'book_id' => $book_id
            ]
        );
    }

}