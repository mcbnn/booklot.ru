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
use VK\VK;
use Application\Entity\Book;

class VkController extends AbstractActionController
{
    protected $app_id = 6728204;
    protected $access_token = '2affee4a5c672abb39b2b02ac2c1c4a8269d540538b72997731b5c25dc8e79c3090cea169adf5917a78b2';
    protected $owner_id = '-35938977';
    protected $vk = null;
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

    public function vkAction()
    {
        $this->vk = new VK($this->app_id, '', $this->access_token);
        if(!$this->vk->isAuth())
        {
            echo 'Вы не авторизованны';
            die();
        }
        if($book = $this->getBook())
        {
            /** @var  \Application\Entity\Book $book */
            $message = $book->getName().PHP_EOL;
            $message .= 'Год: '.$book->getYear().PHP_EOL;
            $message .= 'Кол-во страниц: '.$book->getKolStr().PHP_EOL;
            $message .= 'Ссылка: '.$this->getUrl($book).PHP_EOL;
            $message .= strip_tags($book->getTextSmall());
            $this->setPost($message, 'https://www.image.booklot.ru/resize/400/'.$book->getFoto());
        }
    }

    public function getUrl($book = null)
    {
        if(!$book)return;
        $config = $this->sm->get('config');
        $site = $config['BASE_URL'];
        return $site.$this->sm->get(
                'ViewHelperManager'
            )->get('url')->__invoke(
                'home/genre/one/book',
                [
                    'alias_menu' => $book->getNAliasMenu(),
                    's'          => $book->getNS(),
                    'book'       => $book->getAlias(),
                ]
            );
    }

    public function getBook()
    {
        $em = $this->getEntityManager();
        $book = $em->getRepository(Book::class)->findOneByVk();
        if(!$book)return false;
        $book->setVis(1);
        $em->persist($book);
        $em->flush();
        return $book;
    }

    public function getPosts($count = 1)
    {
        return $this->vk->api('wall.get',
            [
                'owner_id' => $this->owner_id,
                'count' => $count,
                'v' => '5.52'
            ]
        );
    }

    public function setPost($message = null, $photo = null)
    {
        if(!$message)return;
        return $this->vk->api('wall.post',
            [
                'friends_only' => 0,
                'message' => $message,
                'owner_id' => $this->owner_id,
                'from_group' => 1,
                'attachments' => 'photo'.$this->owner_id.'_'.rand(11111,324325235423323).','.$photo,
                'v' => '5.52'
            ]
        );
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

}