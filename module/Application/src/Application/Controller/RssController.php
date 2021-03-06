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
        $config = $this->sm->get('Config');
        $feed = new Feed();
        $feed->setTitle('RSS BOOKLOT');
        $feed->setLink('https://www.booklot.org/');
        $feed->setFeedLink('https://www.booklot.org/feed.xml', 'rss');
        $feed->setDescription('Последние добавленные книги');
        $feed->setDateModified(new \DateTime());
        $feed->setLastBuildDate(new \DateTime());
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
            $entry->setLink($config['BASE_URL'].$url);
            if (!empty($item->getTextSmall())) {
                $entry->setDescription($item->getTextSmall());
            }
            $entry->setDateCreated($item->getDateAdd());
            $entry->setDateModified($item->getDateAdd());
            $feed->addEntry($entry);
        }
        $rss = $feed->export('rss');
        header("Content-Type: application/rss+xml; charset=utf-8");
        $rss = str_replace('<item>', '<item turbo="true">', $rss);
        $rss = str_replace('<description>', '<turbo:content>', $rss);
        $rss = str_replace('</description>', '</turbo:content>', $rss);
        echo $rss;
        exit();
    }
}