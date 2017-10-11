<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container as Container;
use Application\Model\Section;
use Admin\Form\SectionForm;

class PayController extends AbstractActionController{

	public function indexAction(){
		$sm = $this->getServiceLocator();
		$order = "pay_user.id_pay_user DESC";
		$payUser = $sm->get('Application\Model\PayUserTable')->fetchAll(false,$order,false);
		return new ViewModel(array('payUser'=>$payUser));
	}

	public function deletePayAction(){
		$sm=$this->getServiceLocator();
		$id_pay_user=$this->params()->fromRoute('id');
		$sm->get('Application\Model\PayUserTable')->delete('id_pay_user',$id_pay_user);
		$sm->get('Application\Model\PayProductTable')->delete('id_pay_user',$id_pay_user);
		$this->redirect()->toRoute('admin/slash/pay',array('action'=>'index','subdomain'=>'admin'));
	}

	public function redactorPayAction(){
		$sm=$this->getServiceLocator();
		$id_pay_user=$this->params()->fromRoute('id');
		$where = "pay_user.id_pay_user = '$id_pay_user'";
		$payUser = $sm->get('Application\Model\PayUserTable')->fetchAll(false,false,$where);
		$payUser = $payUser -> current();
		$where = "pay_product.id_pay_user = '$id_pay_user'";
		$payProduct = $sm->get('Application\Model\PayProductTable')->joinContents() -> joinCategoryBrand() -> fetchAll(false,false,$where);
		$arrNew = array();
		$order = "menu.id_menu_main ASC";
		$where = "menu.id_menu_main > 0";
		$allMenu = $sm->get('Application\Model\MenuTable')->fetchAll(false, $order, $where);
		foreach($payProduct as $v){
			$url = $sm->get("MainController")->searchRouteContents($v -> id_menu, $allMenu).$v -> alias_contents.'/';
			$v -> url = $url;
			$arrNew[] = $v;
		}

		return new ViewModel(array('payUser'=>$payUser,'payProduct'=>$arrNew));
	}
	public function deleteProductAction(){
		$sm=$this->getServiceLocator();
		$id_pay_product=$this->params()->fromRoute('id');
		$where = "pay_product.id_pay_product = '$id_pay_product'";
		$product = $sm->get('Application\Model\PayProductTable')->fetchAll(false,false,$where);
		$product = $product -> current();
		$sm->get('Application\Model\PayProductTable')->delete('id_pay_product',$id_pay_product);
		$this->redirect()->toRoute('admin/slash/pay',array('action'=>'redactorPay','subdomain'=>'admin',
		                                                   'id' => $product -> id_pay_user));
	}
	public function redactorProductAction(){
		$sm=$this->getServiceLocator();
		$id_pay_product=$this->params()->fromRoute('id');
		$where = "pay_product.id_pay_product = '$id_pay_product'";
		$product = $sm->get('Application\Model\PayProductTable')->fetchAll(false,false,$where);
		$product = $product -> current();
		$vis_pay_product = 0;
		if($product->vis_pay_product==0){
			$vis_pay_product = 1;
		}
		$arr = array();
		$arr['vis_pay_product'] = $vis_pay_product;
		$sm->get('Application\Model\PayProductTable')->save($arr, $id_pay_product, 'id_pay_product');
		$this->redirect()->toRoute('admin/slash/pay',array('action'=>'redactorPay','subdomain'=>'admin',
		                                                   'id' => $product -> id_pay_user));
	}
	public function listPayProductAction(){
		$sm=$this->getServiceLocator();
		$order = "pay_product.id_pay_product DESC";
		$payProduct = $sm->get('Application\Model\PayProductTable')->joinContents() -> joinCategoryBrand() ->fetchAll(false,$order);
		return new ViewModel(array('payProduct'=>$payProduct));
	}


}
