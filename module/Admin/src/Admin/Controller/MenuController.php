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
use Application\Model\Menu;
use Admin\Form\MenuForm;

class MenuController extends AbstractActionController{
	public function indexAction(){
		$sm = $this->getServiceLocator();
		$order = "menu.id_menu DESC";
		$menu = $sm->get( 'Application\Model\MenuTable' )->joinParentMenu()->fetchAll( false, $order, false );
		return new ViewModel( array( 'menu' => $menu ) );
	}

	public function denroidListAction(){
		$sm = $this->getServiceLocator();
		$order = "menu.id_menu_main ASC";
		$menu = $sm->get( 'Application\Model\MenuTable' )->joinType()->fetchAll( false, $order, false );
		$fetchMenuArray = array();
		foreach( $menu as $v ){
			$fetchMenuArray[ ] = (array)$v;
		}
		$menuAll = $this->genMenuDendroid( 0, $fetchMenuArray );
		return new ViewModel( array( 'menu' => $menuAll ) );
	}

	public function addMenuAction(){
		$sm = $this->getServiceLocator();
		$form = new MenuForm();
		$fromFile = $this->params()->fromFiles();
		$request = $this->getRequest();
		$update = $request->getPost( 'send' );
		if( isset( $update ) ){
			if( $request->isPost() ){
				$menu = new Menu();
				$form->setInputFilter( $menu->getInputFilter() );
				$form->setData( $request->getPost() );
				if( $form->isValid() ){
					$menu->exchangeArray( $form->getData() );
					$mainController = new MainController;
					$name = $mainController->fotoSave( $fromFile[ 'foto_menu' ] );
					$menu -> foto_menu = $name;
					$sm->get( 'Application\Model\MenuTable' )->save( $menu, false, false, true );
					$this->redirect()
						->toRoute( 'admin/slash/menu', array( 'action' => 'index', 'subdomain' => 'admin' ) );
				}
			}
		}
		$order = "menu.id_menu_main ASC";
		$menu = $sm->get( 'Application\Model\MenuTable' )->fetchAll( false, $order, false );
		$fetchMenuArray = array();
		foreach( $menu as $v ){
			$fetchMenuArray[ ] = (array)$v;
		}
		$menuAll = $this->genMenu( 0, $fetchMenuArray );
		$type = $sm->get( 'Application\Model\TypeTable' )->fetchAll( false );
		return new ViewModel( array( 'menu' => $menuAll, 'form' => $form, 'type' => $type ) );
	}

	public function redactorMenuAction(){
		$sm = $this->getServiceLocator();
		$id_menu = $this->params()->fromRoute( 'id' );
		$fromFile = $this->params()->fromFiles();
		$form = new MenuForm();
		$request = $this->getRequest();
		$update = $request->getPost( 'send' );
		$del = $request->getPost( 'del' );
		if( isset( $update ) ){
			if( $request->isPost() ){
				$menu = new Menu();
				$form->setInputFilter( $menu->getInputFilter() );
				$form->setData( $request->getPost() );
				if( $form->isValid() ){
					$menu->exchangeArray( $form->getData() );
					$mainController = new MainController;
					$foto_name = $mainController->fotoSave( $fromFile[ 'foto_menu' ] );

					$del_foto = $request->getPost( 'del_foto' );
					if( isset( $del_foto ) and ! empty( $del_foto ) ){
						$menu -> foto_menu = '';
					}
					elseif( ! empty( $foto_name ) ){
						$menu -> foto_menu = $foto_name;
					}
					//print_r($menu);die();
					$sm->get( 'Application\Model\MenuTable' )->save( $menu, $id_menu, 'id_menu' );
					$this->redirect()
						->toRoute( 'admin/slash/menu', array( 'action' => 'index', 'subdomain' => 'admin' ) );
				}
			}
		}
		if( isset( $del ) ){
			$sm->get( 'Application\Model\MenuTable' )->delete( 'id_menu', $id_menu );
			$this->redirect()->toRoute( 'admin/slash/menu', array( 'action' => 'index', 'subdomain' => 'admin' ) );
		}
		$order = "menu.id_menu_main ASC";
		$menu = $sm->get( 'Application\Model\MenuTable' )->joinType()->fetchAll( false, $order, false );
		$fetchMenuArray = array();
		foreach( $menu as $v ){
			$fetchMenuArray[ ] = (array)$v;
		}
		$menuAll = $this->genMenu( 0, $fetchMenuArray );
		$where = "menu.id_menu='$id_menu'";
		$menuSelf = $sm->get( 'Application\Model\MenuTable' )->fetchAll( false, false, $where );
		$menuSelf = $menuSelf->current();
		$typeCheck = $this->checkType( $menuSelf->id_menu_main, $fetchMenuArray );
		$type = $sm->get( 'Application\Model\TypeTable' )->fetchAll( false );

		return new ViewModel( array( 'menu' => $menuAll, 'menuSelf' => $menuSelf, 'form' => $form, 'type' => $type, 'typeCheck' => $typeCheck ) );
	}

	public function deleteMenuAction(){
		$sm = $this->getServiceLocator();
		$id_menu = $this->params()->fromRoute( 'id' );
		$sm->get( 'Application\Model\MenuTable' )->delete( 'id_menu', $id_menu );
		$this->redirect()->toRoute( 'admin/slash/menu', array( 'action' => 'index', 'subdomain' => 'admin' ) );
	}

	public function genMenuDendroid( $id_parent_menu, $menuAll ){
		$menu = '';
		foreach( $menuAll as $v ){

			if( $id_parent_menu == $v[ 'id_menu_main' ] ){
				$children = $this->genMenuDendroid( $v[ 'id_menu' ], $menuAll );
				if( ! empty( $children ) ){
					$menu .= '<li><a href="/menu/redactorMenu/' . $v[ 'id_menu' ] . '">' . $v[ 'label_menu' ] . ' / <small>' . $v[ 'name_type' ] . '</small></a><ul class="nav nav-list">' . $children . '</ul></li>';
				}
				else{
					$menu .= '<li><a href="/menu/redactorMenu/' . $v[ 'id_menu' ] . '">' . $v[ 'label_menu' ] . ' / <small>' . $v[ 'name_type' ] . '</small></a>' . $children . '</li>';
				}
			}
		}
		return $menu;
	}


	public function genMenu( $id_parent_menu, $menuAll, $t = 0 ){
		$menu = array();
		$t ++;
		foreach( $menuAll as &$v ){
			if( $id_parent_menu == $v[ 'id_menu_main' ] ){
				$tire = "";
				for( $i = 0; $i <= ( $t - 1 ); $i ++ ){
					$tire .= "--";
				}
				$menu[ $v[ 'id_menu' ] ] = $v;
				$menu[ $v[ 'id_menu' ] ][ 'labelMenuTire' ] = $tire . $v[ 'label_menu' ];
				$children = $this->genMenu( $v[ 'id_menu' ], $menuAll, $t );
				if( ! empty( $children ) ){
					foreach( $children as $b => $n ){
						$menu[ $b ] = $n;
					}
				}
			}
		}
		return $menu;
	}

	public function checkType( $id_parent_menu, $menuAll ){

		foreach( $menuAll as $v ){
			if( $id_parent_menu == $v[ 'id_menu' ] ){
				if( ! empty( $v[ 'id_type' ] ) ){
					return $v;
				}
				$children = $this->checkType( $v[ 'id_menu_main' ], $menuAll );
				if( ! empty( $children ) ){
					return $children;
				}
			}
		}
		return false;
	}

}
