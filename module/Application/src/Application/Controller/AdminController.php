<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Form\Element\DateTime;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Expression;
use Application\Controller\MainController;
use Admin\Form\RegForm;
use Zend\View\Model\JsonModel;

class AdminController extends AbstractActionController
{

	public $user;

	public function indexAction()
	{
		$this->check();

	}

	public function viewfb2Action()
	{	ini_set('display_errors',1);
		$this->check();
		$file = $this->params()->fromFiles('file');

		if(!empty($file)){
			$name_file = $file['name'];
			$content = file_get_contents($file['tmp_name']);
			$doc = new \DOMDocument();
			$doc->strictErrorChecking = false;
			$doc->recover = true;

			$load = $doc->loadXML($content,LIBXML_NOERROR);
			if (!$load) {
			echo "Ошибка загрузки!";
						die();
			}
			$description = $doc->getElementsByTagName('description');
			$description = $description->item(0);
			if (!$description) {
			echo "No description!"; die();
			}
			$title_info = $description->getElementsByTagName('title-info')->item(0);
			$genre_list = $title_info->getElementsByTagName('genre');
			if (count($genre_list)==0){ $fb2error=1; }
			foreach ($genre_list as $element ){
				$genres[] = $element->nodeValue;
			}
			$authors_list = $title_info->getElementsByTagName('author');
			$element = '';
			if (count($authors_list)==0){ $fb2error=1; }
			foreach ($authors_list as $element) {
					$element->getElementsByTagName('last-name')->item(0)->nodeValue;
					$element->getElementsByTagName('middle-name')->item(0)->nodeValue;
					$element->getElementsByTagName('nickname')->item(0)->nodeValue;
					$element->getElementsByTagName('email')->item(0)->nodeValue;
			}
			$translator = $title_info->getElementsByTagName('translator');
			$element = '';
			foreach ($translator as $element) {
					$element->getElementsByTagName('last-name')->item(0)->nodeValue;
					$element->getElementsByTagName('middle-name')->item(0)->nodeValue;
					$element->getElementsByTagName('nickname')->item(0)->nodeValue;
					$element->getElementsByTagName('email')->item(0)->nodeValue;
			}
			$book_title = $title_info->getElementsByTagName('book-title')->item(0)->nodeValue;
			$annotation = $title_info->getElementsByTagName('annotation')->item(0)->nodeValue;
			$date = $title_info->getElementsByTagName('date')->item(0)->nodeValue;


			var_dump($genres);
			die();
		}




	}

	public function check(){
		$this->user = $this->getServiceLocator()->get('AuthService')->getIdentity();
		if($this->user->role != 'admin')return $this->redirect()->toRoute('home/slash', array('subdomain' => 'booklot.ru'));
	}


}
