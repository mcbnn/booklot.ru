<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 05.12.17
 * Time: 13:32
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Feed\Writer\Feed;
use Zend\Diactoros\Response\TextResponse;
use Application\Entity\Book;
use Zend\Stdlib\DateTime;
use Zend\View\Model\ViewModel;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator as ZendPaginator;


class RssController extends AbstractActionController
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em = null;

    protected function getEntityManager()
    {
        if ($this->em == null) {
            $this->em = $this->getServiceLocator()->get(
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

        $em = $this->getEntityManager();
        $repository = $em->getRepository(Book::class);
        $findBy = $repository->findBy(['vis' => 1], ['id' => 'DESC'], 30);

        foreach ($findBy as $item) {
            $entry = $feed->createEntry();
            $entry->setTitle($item->getName());
            $params = [];
            $params['book'] = $item->getAlias();
            $params['s'] = $item->getZhanr()[0]->getParent()->getAlias();
            $params['alias_menu'] = $item->getZhanr()[0]->getAlias();
            $route_ = 'home/genre/one/book';
            $url = $this->getServiceLocator()->get('ViewHelperManager')->get(
                'url'
            )->__invoke($route_, $params);
            $entry->setLink($url);
            $entry->addAuthor(['name' => $item->getAvtor()[0]->getName()]);
            $entry->setDateCreated($item->getDateAdd());
            $entry->setDateModified($item->getDateAdd());
            if (!empty($item->getTextSmall())) {
                $entry->setContent($item->getTextSmall());
            }
            $feed->addEntry($entry);
        }
        $rss = $feed->export('atom');

        echo $rss;

        exit();
    }
}