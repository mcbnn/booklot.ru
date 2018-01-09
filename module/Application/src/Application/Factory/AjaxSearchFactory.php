<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Entity\Book;
use Application\Entity\MZhanr;
use Application\Entity\MAvtor;
use Application\Entity\MSerii;
use Application\Entity\MTranslit;

class AjaxSearchFactory implements FactoryInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em = null;

    /**
     * @param ServiceLocatorInterface $sm
     *
     * @return \Doctrine\ORM\EntityManager|mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function getEntityManager(ServiceLocatorInterface $sm)
    {
        if ($this->em == null) {
            $this->em = $sm->get(
                'doctrine.entitymanager.orm_default'
            );
        }
        return $this->em;
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $sm = $container->get('ServiceManager');
        $data = $sm->get('request')->getQuery()->get('term');
        $em = $this->getEntityManager($sm);
        if (empty($data)) {
            return [];
        }
        $arr = [];
        switch ($data['name']) {

            case 'b_name':
                /** @var  $repository \Application\Repository\BookRepository */
                $repository = $em
                    ->getRepository(Book::class );
                $books = $repository->findByBookName($data['value']);
                foreach ($books as $book) {
                    $arr[$book->getId()]['id'] = $book->getName();
                    $arr[$book->getId()]['value'] = $book->getName();
                    $arr[$book->getId()]['label'] = $book->getName();
                }
                break;
            case 'b_nameZhanr':
                /** @var  $repository \Application\Repository\MZhanrRepository */
                $repository = $em
                    ->getRepository(MZhanr::class );
                $zhanrs = $repository->getChild($data['value']);
                foreach ($zhanrs as $zhanr) {
                    $arr[$zhanr->getId()]['id'] = $zhanr->getName();
                    $arr[$zhanr->getId()]['value'] = $zhanr->getName();
                    $arr[$zhanr->getId()]['label'] = $zhanr->getName();
                }
                break;
            case 'ma_name':
                /** @var  $repository \Application\Repository\MAvtorRepository */
                $repository = $em
                    ->getRepository(MAvtor::class );
                $avtors = $repository->getAvtorsName($data['value']);
                foreach ($avtors as $avtor) {
                    $arr[$avtor->getId()]['id'] = $avtor->getName();
                    $arr[$avtor->getId()]['value'] = $avtor->getName();
                    $arr[$avtor->getId()]['label'] = $avtor->getName();
                }
                break;
            case 'ms_name':
                /** @var  $repository \Application\Repository\MSeriiRepository */
                $repository = $em
                    ->getRepository(MSerii::class );
                $serii = $repository->getSeriiName($data['value']);
                foreach ($serii as $seri) {
                    $arr[$seri->getId()]['id'] = $seri->getName();
                    $arr[$seri->getId()]['value'] = $seri->getName();
                    $arr[$seri->getId()]['label'] = $seri->getName();
                }
                break;
            case 'mt_name':
                /** @var  $repository \Application\Repository\MtranslitRepository */
                $repository = $em
                    ->getRepository(MTranslit::class );
                $translits = $repository->getTranslitName($data['value']);
                foreach ($translits as $translit) {
                    $arr[$translit->getId()]['id'] = $translit->getName();
                    $arr[$translit->getId()]['value'] = $translit->getName();
                    $arr[$translit->getId()]['label'] = $translit->getName();
                }
                break;
            case 'b_year':
                /** @var  $repository \Application\Repository\BookRepository */
                $repository = $em
                    ->getRepository(Book::class );
                $years = $repository->findByYears($data['value']);
                foreach ($years as $year) {
                    $arr[$year['year']]['id'] = $year['year'];
                    $arr[$year['year']]['value'] = $year['year'];
                    $arr[$year['year']]['label'] = $year['year'];
                }
                break;
            case 'b_isbn':
                /** @var  $repository \Application\Repository\BookRepository */
                $repository = $em
                    ->getRepository(Book::class );
                $isbns = $repository->findByISBN($data['value']);
                foreach ($isbns as $isbn) {
                    $arr[$isbn['isbn']]['id'] = $isbn['isbn'];
                    $arr[$isbn['isbn']]['value'] = $isbn['isbn'];
                    $arr[$isbn['isbn']]['label'] = $isbn['isbn'];
                }
                break;
            case 'b_city':
                /** @var  $repository \Application\Repository\BookRepository */
                $repository = $em
                    ->getRepository(Book::class );
                $citys = $repository->findByCity($data['value']);
                foreach ($citys as $city) {
                    $arr[$city['city']]['id'] = $city['city'];
                    $arr[$city['city']]['value'] = $city['city'];
                    $arr[$city['city']]['label'] = $city['city'];
                }
                break;
            case 'b_lang':
                /** @var  $repository \Application\Repository\BookRepository */
                $repository = $em
                    ->getRepository(Book::class );
                $langs = $repository->findByLang($data['value']);
                foreach ($langs as $lang) {
                    $arr[$lang['lang']]['id'] = $lang['lang'];
                    $arr[$lang['lang']]['value'] = $lang['lang'];
                    $arr[$lang['lang']]['label'] = $lang['lang'];
                }
                break;
            case 'b_langOr':
                /** @var  $repository \Application\Repository\BookRepository */
                $repository = $em
                    ->getRepository(Book::class );
                $langs = $repository->findByLangOr($data['value']);
                foreach ($langs as $lang) {
                    $arr[$lang['langOr']]['id'] = $lang['langOr'];
                    $arr[$lang['langOr']]['value'] = $lang['langOr'];
                    $arr[$lang['langOr']]['label'] = $lang['langOr'];
                }
                break;
        }
        return $arr;
    }



}