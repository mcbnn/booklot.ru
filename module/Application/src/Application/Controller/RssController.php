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
use Zend\Feed\Writer\Feed;
use Application\Entity\Book;

class RssController extends AbstractActionController
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

    public function indexAction()
    {
        $feed = new Feed();
        $feed->setTitle('RSS BOOKLOT');
        $feed->setLink('https://www.booklot.ru/');
        $feed->setFeedLink('https://www.booklot.ru/feed.xml', 'rss');
        $feed->setDescription('Последние добавленные книги');
        $feed->setDateModified(new \DateTime());
        $feed->remove('head');
        $em = $this->getEntityManager();
        $repository = $em->getRepository(Book::class);
        $findBy = $repository->findBy(['vis' => 1], ['id' => 'DESC'], 30);

        foreach ($findBy as $item) {
            /** @var $item \Application\Entity\Book */
            $entry = $feed->createEntry();
            $entry->setTitle($item->getName());
            $params = [];
            $params['book'] = $item->getAlias();
            $params['s'] = $item->getNS();
            $params['alias_menu'] = $item->getNAliasMenu();
            $route_ = 'home/genre/one/book';
            $url = $this->sm->get('ViewHelperManager')->get(
                'url'
            )->__invoke($route_, $params);
            $entry->setLink($url);
            if(count($item->getAvtor())){
                $entry->addAuthor(
                ['name' => $item->getAvtor()[0]->getName()]
            );
            }
            $entry->setDateCreated($item->getDateAdd());
            $entry->setDateModified($item->getDateAdd());
            if (!empty($item->getTextSmall())) {
                $entry->setContent($item->getTextSmall());
            }
            $feed->addEntry($entry);
        }
        $rss = $feed->export('rss');

        echo $rss;

        exit();
    }
}