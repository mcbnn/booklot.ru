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

    /** @var null  */
    protected $sm = null;

    /**
     * @return null|\Zend\ServiceManager\ServiceLocatorInterface
     */
    protected function getServiceManager(){
        if ($this->sm == null) {
            $this->sm = $this->getServiceLocator();
        }
        return $this->sm;
    }

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
     * @return \Zend\Http\Response|null
     */
    public function convertAction()
    {
        $sm = $this->getServiceManager();
        $config = $sm->get('config');
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
        $documentFb2 = new DocumentFb2($this->getEntityManager(), $sm);
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
        $files = $em->getRepository(FilesParse::class)->findBy([], ['fileId' => 'desc']);
        return new ViewModel(
            [
                'files' => $files,
            ]
        );
    }

    /**
     * @return ViewModel
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id', null);
        if(!$id)return null;
        $em = $this->getEntityManager();
        $fileparse = $em->getRepository(FilesParse::class)->find($id);
        $em->remove($fileparse);
        $em->flush();
        return $this->redirect()->toRoute(
            'home/admin-fb'
        );
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function addAction()
    {
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
                    $hash =time();
                    $nameFile = $hash.'_'.$filename;
                    $adapter->addFilter(
                        'File\Rename',
                        [
                            'target' => 'public/templates/newsave/convert/'
                                .$nameFile,
                            'overwrite' => true,
                        ]
                    );
                    if (!$adapter->receive()) {
                        echo implode("", $adapter->getMessages());
                    }

                    $files_parse_entity = new FilesParse();
                    $files_parse_entity->setName($nameFile);
                    $files_parse_entity->setType(0);
                    $em->persist($files_parse_entity);
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