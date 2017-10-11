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
use Application\Model\Brand;
use Admin\Form\BrandForm;
use Application\Model\Model;
use Admin\Form\ModelForm;
use Admin\Controller\MainController;


class ContentController extends AbstractActionController{

	public function indexAction(){
		$sm = $this->getServiceLocator();
		$order = "contents.datetime_create DESC";
		$where = "type.site_content_type != 1";
		$UserArr = $this->getServiceLocator()->get('AuthService')->getIdentity();
		//print_r($UserArr);die();
		if($UserArr -> role == 'party'){
			$where .= " and (contents.id_moderator = '{$UserArr -> id_users}')";
		}
		if(isset($_GET['bug'])){
			print_r($where);die();
		}

		//$where = false;
		$contents = $sm->get( 'Application\Model\ContentsTable' ) -> joinMenu() -> joinType() ->joinBrand() -> fetchAll( false, $order, $where );
		//print_r($contents -> current());die();






		return new ViewModel( array( 'contents' => $contents ) );
	}

	public function indexSitesAction(){
		$sm = $this->getServiceLocator();
		$order = "contents.datetime_create DESC";
		$where = "type.site_content_type != 0";
		//$where = false;
		$contents = $sm->get( 'Application\Model\ContentsTable' ) -> joinMenu() -> joinType() -> fetchAll( false, $order, $where );
		//print_r($contents -> current());die();
		return new ViewModel( array( 'contents' => $contents ) );
	}

	public function listModelAction(){
		$sm = $this->getServiceLocator();
		$order = "model.id_model DESC";
		$model = $sm->get( 'Application\Model\ModelTable' )->joinBrand()->fetchAll( false, $order, false );

		return new ViewModel( array( 'model' => $model ) );
	}

	public function redactorModelAction(){
		$sm = $this->getServiceLocator();
		$id_model = $this->params()->fromRoute( 'id' );
		$form = new ModelForm();
		$request = $this->getRequest();
		$update = $request->getPost( 'send' );
		$del = $request->getPost( 'del' );
		if( isset( $update ) ){
			if( $request->isPost() ){
				$model = new Model();
				$form->setInputFilter( $model->getInputFilter() );
				$form->setData( $request->getPost() );
				if( $form->isValid() ){
					$model->exchangeArray( $form->getData() );
					$sm->get( 'Application\Model\ModelTable' )->save( $model, $id_model, 'id_model' );
					$this->redirect()
						->toRoute( 'admin/slash/content', array( 'action' => 'listModel', 'subdomain' => 'admin' ) );
				}
			}
		}
		if( isset( $del ) ){
			$sm->get( 'Application\Model\ModelTable' )->delete( 'id_model', $id_model );
			$this->redirect()
				->toRoute( 'admin/slash/content', array( 'action' => 'listModel', 'subdomain' => 'admin' ) );
		}
		$where = "model.id_model='$id_model'";
		$model = $sm->get( 'Application\Model\ModelTable' )->fetchAll( false, false, $where );
		$model = $model->current();
		$order = "brand.order_brand DESC,brand.name_brand ASC";
		$brand = $sm->get( 'Application\Model\BrandTable' )->fetchAll( false, $order, false );

		return new ViewModel( array( 'model' => $model, 'brand' => $brand, 'form' => $form ) );
	}

	public function addModelAction(){
		$sm = $this->getServiceLocator();
		$form = new ModelForm();
		$request = $this->getRequest();
		$update = $request->getPost( 'send' );
		if( isset( $update ) ){
			if( $request->isPost() ){
				$model = new Model();
				$form->setInputFilter( $model->getInputFilter() );
				$form->setData( $request->getPost() );
				if( $form->isValid() ){
					$model->exchangeArray( $form->getData() );
					$sm->get( 'Application\Model\ModelTable' )->save( $model, false, false, true );

					$this->redirect()
						->toRoute( 'admin/slash/content', array( 'action' => 'listModel', 'subdomain' => 'admin' ) );
				}
			}
		}
		$order = "brand.order_brand DESC,brand.name_brand ASC";
		$brand = $sm->get( 'Application\Model\BrandTable' )->fetchAll( false, $order, false );

		return new ViewModel( array( 'form' => $form, 'brand' => $brand ) );
	}

	public function listBrandAction(){
		$sm = $this->getServiceLocator();
		$order = "brand.id_brand DESC";
		$brand = $sm->get( 'Application\Model\BrandTable' )->fetchAll( false, $order, false );

		return new ViewModel( array( 'brand' => $brand ) );
	}

	public function deleteBrandAction(){
		$sm = $this->getServiceLocator();
		$id_brand = $this->params()->fromRoute( 'id' );
		$sm->get( 'Application\Model\BrandTable' )->delete( 'id_brand', $id_brand );
		$sm->get( 'Application\Model\CategoryBrandTable' )->delete( 'id_brand', $id_brand );
		$this->redirect()->toRoute( 'admin/slash/content', array( 'action' => 'listBrand', 'subdomain' => 'admin' ) );
	}

	public function redactorBrandAction(){
		$sm = $this->getServiceLocator();
		$id_brand = $this->params()->fromRoute( 'id' );
		$fromFile = $this->params()->fromFiles();
		$form = new BrandForm();
		$request = $this->getRequest();
		$update = $request->getPost( 'send' );
		$del = $request->getPost( 'del' );

		if( isset( $update ) ){
			if( $request->isPost() ){
				$brand = new Brand();
				$form->setInputFilter( $brand->getInputFilter() );
				$form->setData( $request->getPost() );
				if( $form->isValid() ){
					$brand->exchangeArray( $form->getData() );
					$name_brand = $brand->name_brand;
					$where = "brand.name_brand='$name_brand' and brand.id_brand!='$id_brand'";
					$check = $sm->get( 'Application\Model\BrandTable' )->fetchAll( false, false, $where );
					if( $check->count() != 0 ){
						return $this->redirect()
							->toRoute( 'admin/slash/content', array( 'action' => 'listBrand', 'subdomain' => 'admin' ) );
					}

					$sm->get( 'Application\Model\CategoryBrandTable' )->delete( 'id_brand', $id_brand );
					$category_set = $request->getPost('category_set');
					if(isset($category_set) and !empty($category_set)){
						foreach($category_set as $k => $v){
							$arr = array();
							$arr['id_brand'] = $id_brand;
							$arr['id_category'] = $v;
							$arr['id_cb'] = $k;
							$sm->get( 'Application\Model\CategoryBrandTable' ) -> save( $arr );
						}
					}
					$category = $request->getPost('category');
					if(isset($category) and !empty($category)){
						foreach($category as $v){
							$arr = array();
							$arr['id_brand'] = $id_brand;
							$arr['id_category'] = $v;
							$sm->get( 'Application\Model\CategoryBrandTable' ) -> save( $arr );
						}
					}
					$mainController = new MainController;
					$foto_name = $mainController->fotoSave( $fromFile[ 'foto_brand' ] );
					$del_foto = $request->getPost( 'del_foto' );
					if(empty($brand -> foto_brand) and isset($foto) and !empty($foto)){
						$brand -> foto_brand = $foto;
					}
					if( isset( $del_foto ) and ! empty( $del_foto ) ){
						$brand -> foto_brand = '';
					}
					elseif( ! empty( $foto_name ) ){
						$brand -> foto_brand = $foto_name;
					}
					$sm->get( 'Application\Model\BrandTable' )->save( $brand, $id_brand, 'id_brand' );
					$this->redirect()
						->toRoute( 'admin/slash/content', array( 'action' => 'listBrand', 'subdomain' => 'admin' ) );
				}
			}
		}
		if( isset( $del ) ){
			$sm->get( 'Application\Model\BrandTable' )->delete( 'id_brand', $id_brand );
			$this->redirect()
				->toRoute( 'admin/slash/content', array( 'action' => 'listBrand', 'subdomain' => 'admin' ) );
		}
		$where = "brand.id_brand='$id_brand'";
		$brand = $sm->get( 'Application\Model\BrandTable' )->fetchAll( false, false, $where );
		$brand = $brand->current();
		$category = $sm->get( 'Application\Model\CategoryTable' )->fetchAll( false, false );
		$where = "category_brand.id_brand = '$id_brand'";
		$order = "category.id_category ASC";
		$categoryBrand = $sm->get( 'Application\Model\CategoryBrandTable' )
			->joinCategory()
			->joinBrand()
			->fetchAll( false, $order, $where );
		return new ViewModel( array( 'brand' => $brand, 'form' => $form, 'category' => $category, 'categoryBrand' => $categoryBrand ) );
	}

	public function addBrandAction(){
		$sm = $this->getServiceLocator();
		$form = new BrandForm();
		$request = $this->getRequest();
		$update = $request->getPost( 'send' );
		if( isset( $update ) ){
			if( $request->isPost() ){

				$brand = new Brand();
				$form->setInputFilter( $brand->getInputFilter() );
				$form->setData( $request->getPost() );
				if( $form->isValid() ){
					$brand->exchangeArray( $form->getData() );
					$name_brand = $brand->name_brand;
					$where = "brand.name_brand='$name_brand'";
					$check = $sm->get( 'Application\Model\BrandTable' )->fetchAll( false, false, $where );


					if( $check->count() != 0 ){
						return $this->redirect()
							->toRoute( 'admin/slash/content', array( 'action' => 'listBrand', 'subdomain' => 'admin' ) );
					}
					$id_brand = $sm->get( 'Application\Model\BrandTable' ) -> save( $brand, false, false, true );
					$category = $request->getPost('category');
					if(isset($category) and !empty($category)){
						foreach($category as $v){
							$arr = array();
							$arr['id_brand'] = $id_brand;
							$arr['id_category'] = $v;
							$sm->get( 'Application\Model\CategoryBrandTable' ) -> save( $arr );
						}
					}
					$this->redirect()
						->toRoute( 'admin/slash/content', array( 'action' => 'listBrand', 'subdomain' => 'admin' ) );
				}
			}
		}

		$category = $sm->get( 'Application\Model\CategoryTable' )->fetchAll( false, false );
		return new ViewModel( array( 'form' => $form, 'category' => $category ) );
	}

	public function addContentAction(){
		$UserArr = $this->getServiceLocator()->get('AuthService')->getIdentity();
		$sm = $this->getServiceLocator();
		$time = time();
		$request = $this->getRequest();
		$update = $request->getPost( 'send' );
		$mainFoto = '';
		$main = $this->params()->fromPost( 'main' );
		$foto_url = $this->params()->fromPost( 'foto' );
		if( isset( $update ) ){
			if( $request->isPost() ){
				$arr = array();
				$name_contents = $request->getPost( 'name_contents' );
				if( empty( $name_contents ) ){
					return $this->redirect()
						->toRoute( 'admin/slash/content', array( 'action' => 'addContent', 'subdomain' => 'admin' ) );
				}
				$id_brand = 0;
				$name_brand = $request->getPost( 'name_brand' );
				if( ! empty( $name_brand ) ){
					$where = "brand.name_brand='$name_brand'";
					$brand_check = $sm->get( 'Application\Model\BrandTable' )->fetchAll( false, false, $where );
					if( $brand_check->count() != 0 ){
						$brand_check = $brand_check->current();
						$id_brand = $brand_check->id_brand;
					}
					else{
						$arr = array();
						$arr[ 'name_brand' ] = $name_brand;
						$arr[ 'alias_brand' ] = $this->translitIt( $name_brand );
						$arr[ 'order_brand' ] = 1;
						$id_brand = $sm->get( 'Application\Model\BrandTable' )->save( $arr, false, false, true );
					}
				}
				$id_model = 0;
				$name_model = $request->getPost( 'name_model' );
				if( ! empty( $name_model ) ){
					$where = "model.name_model='$name_model'";
					$model_check = $sm->get( 'Application\Model\ModelTable' )->fetchAll( false, false, $where );
					if( $model_check->count() != 0 ){
						$model_check = $model_check->current();
						$id_model = $model_check->id_model;
					}
					else{
						$arr = array();
						$arr[ 'name_model' ] = $name_model;
						$arr[ 'alias_model' ] = $this->translitIt( $name_model );
						$arr[ 'order_model' ] = 1;
						$arr[ 'id_brand' ] = $id_brand;
						$id_model = $sm->get( 'Application\Model\ModelTable' )->save( $arr, false, false, true );
					}
				}

				$arr = array();
				$arr[ 'name_contents' ] = $request->getPost( 'name_contents' );
				$arr[ 'id_menu' ] = $request->getPost( 'id_menu' );
				$arr[ 'id_type' ] = $request->getPost( 'id_type' );
				$arr[ 'id_brand' ] = $id_brand;
				$arr[ 'id_model' ] = $id_model;
				$arr['id_users'] = $UserArr -> id_users;
				$arr[ 'id_transform' ] = $request->getPost( 'id_transform' );
				$arr[ 'money_contents' ] = $request->getPost( 'money_contents' );
				$arr[ 'alias_contents' ] = $request->getPost( 'alias_contents' );
				$arr[ 'datetime_create' ] = date( "Y-m-d H:i:s", $time );
				$arr[ 'description_contents' ] = $request->getPost( 'description_contents' );
				$arr[ 'keywords_contents' ] = $request->getPost( 'keywords_contents' );
				$arr[ 'vis_contents' ] = ($request->getPost( 'vis_contents' ) == 'on')? 1: 0;
				$id_contents = $sm->get( 'Application\Model\ContentsTable' )->save( $arr, false, false, true );
				$id_type_field = $request->getPost( 'id_type_field' );
				foreach( $id_type_field as $k => $v ){
					$idTypeField = $k;
					$where = "type_field.id_type_field='$idTypeField'";
					$typeField = $sm->get( 'Application\Model\TypeFieldTable' )->fetchAll( false, false, $where );
					$typeField = $typeField->current();
					$arr = array();
					$arr[ 'id_type_field' ] = $idTypeField;
					$arr[ 'id_contents' ] = $id_contents;
					$arr[ $typeField->code_field ] = $v;
					$sm->get( 'Application\Model\ValuesTable' )->save( $arr );
				}
				$File = $this->params()->fromFiles( 'foto' );

				$FileTwo = $this->params()->fromFiles( 'file' );
				$main = $this->params()->fromPost( 'main' );
				$FileName = $this->params()->fromPost( 'name_news_file' );
				if( isset( $File ) and ! empty( $File ) ){
					foreach( $File as $k => $v ){
						if( ! empty( $v[ 'name' ] ) ){
							$mainController = new MainController;
							$name = $mainController->fotoSave( $v );
							if( ! empty( $name ) ){
								$arr = array();
								$arr[ 'id_contents' ] = $id_contents;
								$arr[ 'name_foto' ] = $name;
								$arr[ 'main_foto' ] = 0;
								if( $main[ 0 ] == $k ){
									$arr[ 'main_foto' ] = 1;
									$mainFoto = $name;
								}
								$sm->get( 'Application\Model\FotoTable' )->save( $arr );
							}
						}
					}
				}

				if(isset($foto_url) and !empty($foto_url)){
					foreach($foto_url as $k => $v){
						if( ! empty( $v ) ){
							$mainController = new MainController;
							$name = $mainController->fotoSaveUrl( $v );
							if( ! empty( $name ) ){
								$arr = array();
								$arr[ 'id_contents' ] = $id_contents;
								$arr[ 'name_foto' ] = $name;
								$arr[ 'main_foto' ] = 0;
								if( $main[ 0 ] == $k ){
									$arr[ 'main_foto' ] = 1;
									$mainFoto = $name;
								}
								$sm->get( 'Application\Model\FotoTable' )->save( $arr );
							}
						}
					}
				}

				if( isset( $FileTwo ) and ! empty( $FileTwo ) ){
					foreach( $FileTwo as $k => $v ){
						if( ! empty( $v[ 'name' ] ) ){
							$mainController = new MainController;
							$name = $mainController->FileSave( $v );
							if( ! empty( $name ) ){
								$arr = array();
								$arr[ 'id_contents' ] = $id_contents;
								$arr[ 'url_file' ] = $name;
								$arr[ 'name_file' ] = ( empty( $FileName[ $k ] ) ) ? "Файл $k" : $FileName[ $k ];
								$sm->get( 'Application\Model\FileTable' )->save( $arr );
							}
						}
					}
				}
				$arr = array();
				$arr[ 'foto_contents' ] = $mainFoto;
				$sm->get( 'Application\Model\ContentsTable' )->save( $arr, $id_contents, 'id_contents' );

				$sm->get( 'Application\Model\MoneyCbTable' )->delete( 'id_contents', $id_contents );
				$money_cb = $name_brand = $request->getPost( 'money_cb' );
				if(!empty($money_cb)){
					foreach($money_cb as $k => $v){
						$arr = array();
						$arr['id_contents'] = $id_contents;
						$arr['id_cb'] = $k;
						$arr['money_cb'] = $v;
						$sm->get( 'Application\Model\MoneyCbTable' )->save( $arr );
					}
				}

				$this->redirect()
					->toRoute( 'admin/slash/content', array( 'action' => 'index', 'subdomain' => 'admin' ) );
			}
		}
		$order = "menu.id_menu_main ASC";
		$menu = $sm->get( 'Application\Model\MenuTable' )->fetchAll( false, $order, false );
		$fetchMenuArray = array();
		foreach( $menu as $v ){
			$fetchMenuArray[ ] = (array)$v;
		}
		$menuAll = $this->genMenu( 0, $fetchMenuArray );

		$type = $sm->get( 'Application\Model\TypeTable' )->fetchAll( false, false, false );
		$transform = $sm->get( 'Application\Model\TransformTable' )->fetchAll( false, false, false );
		return new ViewModel( array( 'menu' => $menuAll, 'type' => $type, 'transform' => $transform ) );
	}

	public function deleteContentAction(){
		$sm = $this->getServiceLocator();
		$id_contents = $this->params()->fromRoute( 'id' );
		$sm->get( 'Application\Model\ContentsTable' )->delete( 'id_contents', $id_contents );
		$sm->get( 'Application\Model\ValuesTable' )->delete( 'id_contents', $id_contents );
		$sm->get( 'Application\Model\FotoTable' )->delete( 'id_contents', $id_contents );
		$sm->get( 'Application\Model\FileTable' )->delete( 'id_contents', $id_contents );
		$sm->get( 'Application\Model\MoneyCbTable' )->delete( 'id_contents', $id_contents );
		$this->redirect()->toRoute( 'admin/slash/content', array( 'action' => 'index', 'subdomain' => 'admin' ) );
	}

	public function redactorContentAction(){
		$UserArr = $this->getServiceLocator()->get('AuthService')->getIdentity();
		$sm = $this->getServiceLocator();
		$time = time();
		$request = $this->getRequest();
		$update = $request->getPost( 'send' );
		$del = $request->getPost( 'del' );
		$id_contents = $this->params()->fromRoute( 'id' );
		$File = $this->params()->fromFiles( 'foto' );
		$FileTwo = $this->params()->fromFiles( 'file' );
		$fotoArr = $this->params()->fromPost( 'foto2' );
		$fileArr = $this->params()->fromPost( 'file' );
		$FileName = $this->params()->fromPost( 'name_news_file' );
		$FileNameUpdate = $this->params()->fromPost( 'name_news_file_update' );
		$main = $this->params()->fromPost( 'main' );
		$foto_url = $this->params()->fromPost( 'foto' );
		$mainFoto = "";
		if( isset( $update ) ){
			if( $request->isPost() ){
				$arr = array();
				$arr[ 'name_contents' ] = $request->getPost( 'name_contents' );
				if( empty( $arr[ 'name_contents' ] ) ){
					return $this->redirect()
						->toRoute( 'admin/slash/content', array( 'action' => 'addContent', 'subdomain' => 'admin' ) );
				}
				$id_brand = 0;
				$name_brand = $request->getPost( 'name_brand' );
				if( ! empty( $name_brand ) ){
					$where = "brand.name_brand='$name_brand'";
					$brand_check = $sm->get( 'Application\Model\BrandTable' )->fetchAll( false, false, $where );
					if( $brand_check->count() != 0 ){
						$brand_check = $brand_check->current();
						$id_brand = $brand_check->id_brand;
					}
					else{
						$arr = array();
						$arr[ 'name_brand' ] = $name_brand;
						$arr[ 'alias_brand' ] = $this->translitIt( $name_brand );
						$arr[ 'order_brand' ] = 1;
						$id_brand = $sm->get( 'Application\Model\BrandTable' )->save( $arr, false, false, true );
					}
				}
				$id_model = 0;
				$name_model = $request->getPost( 'name_model' );
				if( ! empty( $name_model ) ){
					$where = "model.name_model='$name_model'";
					$model_check = $sm->get( 'Application\Model\ModelTable' )->fetchAll( false, false, $where );
					if( $model_check->count() != 0 ){
						$model_check = $model_check->current();
						$id_model = $model_check->id_model;
					}
					else{
						$arr = array();
						$arr[ 'name_model' ] = $name_model;
						$arr[ 'alias_model' ] = $this->translitIt( $name_model );
						$arr[ 'order_model' ] = 1;
						$arr[ 'id_brand' ] = $id_brand;
						$id_model = $sm->get( 'Application\Model\ModelTable' )->save( $arr, false, false, true );
					}
				}
				$arr = array();
				$arr[ 'name_contents' ] = $request->getPost( 'name_contents' );
				$arr[ 'id_menu' ] = $request->getPost( 'id_menu' );
				$arr[ 'id_brand' ] = $id_brand;
				$arr[ 'id_model' ] = $id_model;
				$arr[ 'id_type' ] = $request->getPost( 'id_type' );
				$arr[ 'id_users' ] = $UserArr -> id_users;
				$arr[ 'id_transform' ] = $request->getPost( 'id_transform' );
				$arr[ 'money_contents' ] = $request->getPost( 'money_contents' );
				$arr[ 'alias_contents' ] = $request->getPost( 'alias_contents' );
				$arr[ 'datetime_create' ] = date( "Y-m-d H:i:s", $time );
				$arr[ 'description_contents' ] = $request->getPost( 'description_contents' );
				$arr[ 'keywords_contents' ] = $request->getPost( 'keywords_contents' );
				$arr[ 'vis_contents' ] = ($request->getPost( 'vis_contents' ) == 'on')? 1: 0;
				$sm->get( 'Application\Model\ContentsTable' )->save( $arr, $id_contents, 'id_contents' );
				//print_r($arr);die();
				$id_type_field = $request->getPost( 'id_type_field' );
				$sm->get( 'Application\Model\ValuesTable' )->delete( 'id_contents', $id_contents );
				foreach( $id_type_field as $k => $v ){
					$idTypeField = $k;
					$where = "type_field.id_type_field='$idTypeField'";
					$typeField = $sm->get( 'Application\Model\TypeFieldTable' )->fetchAll( false, false, $where );
					$typeField = $typeField->current();
					$arr = array();
					$arr[ 'id_type_field' ] = $idTypeField;
					$arr[ 'id_contents' ] = $id_contents;
					$arr[ $typeField->code_field ] = $v;
					$sm->get( 'Application\Model\ValuesTable' )->save( $arr );
				}

				$sm->get( 'Application\Model\FotoTable' )->delete( 'id_contents', $id_contents );

				if( isset( $fotoArr ) and ! empty( $fotoArr ) ){
					foreach( $fotoArr as $k => $v ){
						$name = $v;
						if( ! empty( $name ) ){
							$arr = array();
							$arr[ 'id_contents' ] = $id_contents;
							$arr[ 'name_foto' ] = $name;
							$arr[ 'main_foto' ] = 0;
							if( $main[ 0 ] == $k ){
								$arr[ 'main_foto' ] = 1;
								$mainFoto = $name;
							}
							$sm->get( 'Application\Model\FotoTable' )->save( $arr );
						}
					}
				}
				if(isset($foto_url) and !empty($foto_url)){
					foreach($foto_url as $k => $v){
						if( ! empty( $v ) ){
							$mainController = new MainController;
							$name = $mainController->fotoSaveUrl( $v );
							if( ! empty( $name ) ){
								$arr = array();
								$arr[ 'id_contents' ] = $id_contents;
								$arr[ 'name_foto' ] = $name;
								$arr[ 'main_foto' ] = 0;
								if( $main[ 0 ] == $k ){
									$arr[ 'main_foto' ] = 1;
									$mainFoto = $name;
								}
								$sm->get( 'Application\Model\FotoTable' )->save( $arr );
							}
						}
					}
				}
				if( isset( $File ) and ! empty( $File ) ){
					foreach( $File as $k => $v ){
						if( ! empty( $v[ 'name' ] ) ){
							$mainController = new MainController;
							$name = $mainController->fotoSave( $v );
							if( ! empty( $name ) ){
								$arr = array();
								$arr[ 'id_contents' ] = $id_contents;
								$arr[ 'name_foto' ] = $name;
								$arr[ 'main_foto' ] = 0;
								if( $main[ 0 ] == $k ){
									$arr[ 'main_foto' ] = 1;
									$mainFoto = $name;
								}
								$sm->get( 'Application\Model\FotoTable' )->save( $arr );
							}
						}
					}
				}
				$sm->get( 'Application\Model\FileTable' )->delete( 'id_contents', $id_contents );
				if( isset( $fileArr ) and ! empty( $fileArr ) ){
					foreach( $fileArr as $k => $v ){
						$name = $v;
						if( ! empty( $name ) ){
							$arr = array();
							$arr[ 'id_contents' ] = $id_contents;
							$arr[ 'url_file' ] = $name;
							$arr[ 'name_file' ] = ( empty( $FileNameUpdate[ $k ] ) ) ? "Файл $k" : $FileNameUpdate[ $k ];
							$sm->get( 'Application\Model\FileTable' )->save( $arr );
						}
					}
				}
				if( isset( $FileTwo ) and ! empty( $FileTwo ) ){
					foreach( $FileTwo as $k => $v ){
						if( ! empty( $v[ 'name' ] ) ){
							$mainController = new MainController;
							$name = $mainController->FileSave( $v );
							if( ! empty( $name ) ){
								$arr = array();
								$arr[ 'id_contents' ] = $id_contents;
								$arr[ 'url_file' ] = $name;
								$arr[ 'name_file' ] = ( empty( $FileName[ $k ] ) ) ? "Файл $k" : $FileName[ $k ];
								$sm->get( 'Application\Model\FileTable' )->save( $arr );
							}
						}
					}
				}
				$arr = array();
				$arr[ 'foto_contents' ] = $mainFoto;
				$sm->get( 'Application\Model\ContentsTable' )->save( $arr, $id_contents, 'id_contents' );
				$sm->get( 'Application\Model\MoneyCbTable' )->delete( 'id_contents', $id_contents );
				$money_cb = $name_brand = $request->getPost( 'money_cb' );
				if(!empty($money_cb)){
					foreach($money_cb as $k => $v){
						$arr = array();
						$arr['id_contents'] = $id_contents;
						$arr['id_cb'] = $k;
						$arr['money_cb'] = $v;
						$sm->get( 'Application\Model\MoneyCbTable' )->save( $arr );
					}
				}
				$this->redirect()
					->toRoute( 'admin/slash/content', array( 'action' => 'index', 'subdomain' => 'admin' ) );
			}
		}
		if( isset( $del ) ){
			$sm->get( 'Application\Model\ContentsTable' )->delete( 'id_contents', $id_contents );
			$sm->get( 'Application\Model\ValuesTable' )->delete( 'id_contents', $id_contents );
			$sm->get( 'Application\Model\FotoTable' )->delete( 'id_contents', $id_contents );
			$sm->get( 'Application\Model\FileTable' )->delete( 'id_contents', $id_contents );
			$sm->get( 'Application\Model\MoneyCbTable' )->delete( 'id_contents', $id_contents );
			$this->redirect()->toRoute( 'admin/slash/content', array( 'action' => 'index', 'subdomain' => 'admin' ) );
		}
		$where = "contents.id_contents='$id_contents'";
		$contents = $sm->get( 'Application\Model\ContentsTable' )->joinBrand()->joinModel()
			->fetchAll( false, false, $where );
		$contents = $contents->current();


		$UserArr = $this->getServiceLocator()->get('AuthService')->getIdentity();
		if(($UserArr -> role == 'party' and  $contents -> id_moderator != $UserArr -> id_users)){
			die();
		}

		$where = "type_field.id_type = '".$contents -> id_type."'";
		$order = "type_field.order_type_field DESC";
		$values = $sm->get( 'Application\Model\ValuesTable' )
			->joinTypeField("values.id_contents='$id_contents'")
			->joinSection()
			->joinField()
			->fetchAll( false, $order, $where );
		$valuesArr = array();
		foreach( $values as $v ){
			$codeText = $v->code_field;
			$arr = array();
			$gen_field = "";
			switch( $v->code_field ){
				case 'varchar_value':
					$gen_field = "<input value='{$v->$codeText}' class='varchartext' name='id_type_field[" . $v->id_type_field . "]' type='text' />";
					break;
				case 'text_value':
					$gen_field = "<textarea class='wiswigs' name='id_type_field[" . $v -> id_type_field . "]'>{$v->$codeText}</textarea>";
					break;
				case 'date_value':
					$gen_field = '<div   class="input-append date datepicker"><input value="' . $v->$codeText . '" class="dateimecss" name="id_type_field[' . $v->id_type_field . ']" data-format="yyyy-MM-dd" type="text"></input><span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span></div>';
					break;
				case 'datetime_value':
					$gen_field = '<div   class="input-append date datetimepicker"><input value="' . $v->$codeText . '" class="dateimecss" name="id_type_field[' . $v->id_type_field . ']" data-format="yyyy-MM-dd hh:mm:00" type="text"></input><span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span></div>';
					break;
				case 'int_value':
					$gen_field = "<input value='{$v->$codeText}' class='integer' name='id_type_field[" . $v->id_type_field . "]' type='text' />";
					break;
				case 'text_small_value':
					$gen_field = "<textarea class='wiswigs' name='id_type_field[" . $v->id_type_field . "]'>{$v->$codeText}</textarea>";
					break;
			}
			$arr = (array)$v;
			$arr[ 'gen_field' ] = $gen_field;
			$valuesArr[ $v->id_type_field ] = $arr;
		}
		$order = "type.id_type ASC";
		$type = $sm->get( 'Application\Model\TypeTable' )->fetchAll( false, $order );
		$order = "menu.id_menu_main ASC";
		$menu = $sm->get( 'Application\Model\MenuTable' )->fetchAll( false, $order, false );
		$fetchMenuArray = array();
		foreach( $menu as $v ){
			$fetchMenuArray[ ] = (array)$v;
		}

		$menuAll = $this->genMenu( 0, $fetchMenuArray );

		$order = "foto.id_foto ASC";
		$where = "foto.id_contents = '$id_contents'";
		$foto = $sm->get( 'Application\Model\FotoTable' )->fetchAll( false, $order, $where );
		$order = "file.id_file ASC";
		$where = "file.id_contents = '$id_contents'";
		$file = $sm->get( 'Application\Model\FileTable' )->fetchAll( false, $order, $where );
		$categoryBrand = false;
		if(!empty($contents -> id_brand)){
			$where = "category_brand.id_brand = '{$contents -> id_brand}'";
			$order = "category.id_category ASC";
			$categoryBrand = $sm->get( 'Application\Model\CategoryBrandTable' )
				->joinCategory()
				->joinBrand()
				->joinMoneyCb($id_contents)
				->fetchAll( false, $order, $where );
		}
		$transform = $sm->get( 'Application\Model\TransformTable' )->fetchAll( false, false, false );


		$order = "menu.id_menu_main ASC";
		$where = "menu.id_menu_main > 0";
		$allMenu = $sm->get('Application\Model\MenuTable')->fetchAll(false, $order, $where);
		$allMenuArray = array();
		foreach($allMenu as $v){
			$allMenuArray[] = (array)$v;
		}

		$href = "";
		$glav = "";
		$glav = $sm->get("MainController")->searchRouteContents($contents -> id_menu, $allMenuArray);
		$href = "http://nemebel.ru/".$glav.$contents -> alias_contents.'/';

		return new ViewModel(
			array(
				'type' => $type,
				'menu' => $menuAll,
				'contents' => $contents,
				'values' => $valuesArr,
				'foto' => $foto,
				'file' => $file,
				'categoryBrand' => $categoryBrand,
				'transform' => $transform,
				'url' => $href
			) );
	}


	public function copyContentAction(){
		$sm = $this->getServiceLocator();
		$time = time();
		$request = $this->getRequest();
		$update = $request->getPost( 'send' );
		$del = $request->getPost( 'del' );
		$id_contents = $this->params()->fromRoute( 'id' );
		$File = $this->params()->fromFiles( 'foto' );
		$FileTwo = $this->params()->fromFiles( 'file' );
		$fotoArr = $this->params()->fromPost( 'foto' );
		$fileArr = $this->params()->fromPost( 'file' );
		$FileName = $this->params()->fromPost( 'name_news_file' );
		$FileNameUpdate = $this->params()->fromPost( 'name_news_file_update' );
		$main = $this->params()->fromPost( 'main' );
		$mainFoto = "";
		if( isset( $update ) ){
			if( $request->isPost() ){
				$arr = array();
				$name_contents = $request->getPost( 'name_contents' );
				if( empty( $name_contents ) ){
					return $this->redirect()
						->toRoute( 'admin/slash/content', array( 'action' => 'addContent', 'subdomain' => 'admin' ) );
				}
				$id_brand = 0;
				$name_brand = $request->getPost( 'name_brand' );
				if( ! empty( $name_brand ) ){
					$where = "brand.name_brand='$name_brand'";
					$brand_check = $sm->get( 'Application\Model\BrandTable' )->fetchAll( false, false, $where );
					if( $brand_check->count() != 0 ){
						$brand_check = $brand_check->current();
						$id_brand = $brand_check->id_brand;
					}
					else{
						$arr = array();
						$arr[ 'name_brand' ] = $name_brand;
						$arr[ 'alias_brand' ] = $this->translitIt( $name_brand );
						$arr[ 'order_brand' ] = 1;
						$id_brand = $sm->get( 'Application\Model\BrandTable' )->save( $arr, false, false, true );
					}
				}
				$id_model = 0;
				$name_model = $request->getPost( 'name_model' );
				if( ! empty( $name_model ) ){
					$where = "model.name_model='$name_model'";
					$model_check = $sm->get( 'Application\Model\ModelTable' )->fetchAll( false, false, $where );
					if( $model_check->count() != 0 ){
						$model_check = $model_check->current();
						$id_model = $model_check->id_model;
					}
					else{
						$arr = array();
						$arr[ 'name_model' ] = $name_model;
						$arr[ 'alias_model' ] = $this->translitIt( $name_model );
						$arr[ 'order_model' ] = 1;
						$arr[ 'id_brand' ] = $id_brand;
						$id_model = $sm->get( 'Application\Model\ModelTable' )->save( $arr, false, false, true );
					}
				}

				$arr = array();
				$arr[ 'name_contents' ] = $request->getPost( 'name_contents' );
				$arr[ 'id_menu' ] = $request->getPost( 'id_menu' );
				$arr[ 'id_brand' ] = $id_brand;
				$arr[ 'id_model' ] = $id_model;
				$arr[ 'money_contents' ] = $request->getPost( 'money_contents' );
				$arr[ 'alias_contents' ] = $request->getPost( 'alias_contents' );
				$arr[ 'datetime_create' ] = date( "Y-m-d H:i:s", $time );
				$id_contents = $sm->get( 'Application\Model\ContentsTable' )->save( $arr, false, false, true );

				$id_type_field = $request->getPost( 'id_type_field' );
				foreach( $id_type_field as $k => $v ){
					$idTypeField = $k;
					$where = "type_field.id_type_field='$idTypeField'";
					$typeField = $sm->get( 'Application\Model\TypeFieldTable' )->fetchAll( false, false, $where );
					$typeField = $typeField->current();
					$arr = array();
					$arr[ 'id_type_field' ] = $idTypeField;
					$arr[ 'id_contents' ] = $id_contents;
					$arr[ $typeField->code_field ] = $v;
					$sm->get( 'Application\Model\ValuesTable' )->save( $arr );
				}

				$sm->get( 'Application\Model\FotoTable' )->delete( 'id_contents', $id_contents );
				if( isset( $fotoArr ) and ! empty( $fotoArr ) ){
					foreach( $fotoArr as $k => $v ){
						$name = $v;
						if( ! empty( $name ) ){
							$arr = array();
							$arr[ 'id_contents' ] = $id_contents;
							$arr[ 'name_foto' ] = $name;
							$arr[ 'main_foto' ] = 0;
							if( $main[ 0 ] == $k ){
								$arr[ 'main_foto' ] = 1;
								$mainFoto = $name;
							}
							$sm->get( 'Application\Model\FotoTable' )->save( $arr );
						}
					}
				}
				if( isset( $File ) and ! empty( $File ) ){
					foreach( $File as $k => $v ){
						if( ! empty( $v[ 'name' ] ) ){
							$mainController = new MainController;
							$name = $mainController->fotoSave( $v );
							if( ! empty( $name ) ){
								$arr = array();
								$arr[ 'id_contents' ] = $id_contents;
								$arr[ 'name_foto' ] = $name;
								$arr[ 'main_foto' ] = 0;
								if( $main[ 0 ] == $k ){
									$arr[ 'main_foto' ] = 1;
									$mainFoto = $name;
								}
								$sm->get( 'Application\Model\FotoTable' )->save( $arr );
							}
						}
					}
				}
				$sm->get( 'Application\Model\FileTable' )->delete( 'id_contents', $id_contents );
				if( isset( $fileArr ) and ! empty( $fileArr ) ){
					foreach( $fileArr as $k => $v ){
						$name = $v;
						if( ! empty( $name ) ){
							$arr = array();
							$arr[ 'id_contents' ] = $id_contents;
							$arr[ 'url_file' ] = $name;
							$arr[ 'name_file' ] = ( empty( $FileNameUpdate[ $k ] ) ) ? "Файл $k" : $FileNameUpdate[ $k ];
							$sm->get( 'Application\Model\FileTable' )->save( $arr );
						}
					}
				}
				if( isset( $FileTwo ) and ! empty( $FileTwo ) ){
					foreach( $FileTwo as $k => $v ){
						if( ! empty( $v[ 'name' ] ) ){
							$mainController = new MainController;
							$name = $mainController->FileSave( $v );
							if( ! empty( $name ) ){
								$arr = array();
								$arr[ 'id_contents' ] = $id_contents;
								$arr[ 'url_file' ] = $name;
								$arr[ 'name_file' ] = ( empty( $FileName[ $k ] ) ) ? "Файл $k" : $FileName[ $k ];
								$sm->get( 'Application\Model\FileTable' )->save( $arr );
							}
						}
					}
				}
				$arr = array();
				$arr[ 'foto_contents' ] = $mainFoto;
				$sm->get( 'Application\Model\ContentsTable' )->save( $arr, $id_contents, 'id_contents' );
				$sm->get( 'Application\Model\MoneyCbTable' )->delete( 'id_contents', $id_contents );
				$money_cb = $name_brand = $request->getPost( 'money_cb' );
				if(!empty($money_cb)){
					foreach($money_cb as $k => $v){
						$arr = array();
						$arr['id_contents'] = $id_contents;
						$arr['id_cb'] = $k;
						$arr['money_cb'] = $v;
						$sm->get( 'Application\Model\MoneyCbTable' )->save( $arr );
					}
				}
				$this->redirect()
					->toRoute( 'admin/slash/content', array( 'action' => 'index', 'subdomain' => 'admin' ) );
			}
		}
		$where = "contents.id_contents='$id_contents'";
		$contents = $sm->get( 'Application\Model\ContentsTable' )->joinBrand()->joinModel()
			->fetchAll( false, false, $where );
		$contents = $contents->current();
		$where = "values.id_contents='$id_contents'";
		$order = "type_field.order_type_field DESC";
		$values = $sm->get( 'Application\Model\ValuesTable' )->joinTypeField()->joinSection()->joinField()
			->fetchAll( false, $order, $where );
		$valuesArr = array();
		foreach( $values as $v ){
			$codeText = $v->code_field;
			$arr = array();
			$gen_field = "";
			switch( $v->code_field ){
				case 'varchar_value':
					$gen_field = "<input value='{$v->$codeText}' class='varchartext' name='id_type_field[" . $v->id_type_field . "]' type='text' />";
					break;
				case 'text_value':
					$gen_field = "<textarea class='wiswigs' name='id_type_field[" . $v->id_type_field . "]'>{$v->$codeText}</textarea>";
					break;
				case 'date_value':
					$gen_field = '<div   class="input-append date datepicker"><input value="' . $v->$codeText . '" class="dateimecss" name="id_type_field[' . $v->id_type_field . ']" data-format="yyyy-MM-dd" type="text"></input><span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span></div>';
					break;
				case 'datetime_value':
					$gen_field = '<div   class="input-append date datetimepicker"><input value="' . $v->$codeText . '" class="dateimecss" name="id_type_field[' . $v->id_type_field . ']" data-format="yyyy-MM-dd hh:mm:00" type="text"></input><span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span></div>';
					break;
				case 'int_value':
					$gen_field = "<input value='{$v->$codeText}' class='integer' name='id_type_field[" . $v->id_type_field . "]' type='text' />";
					break;
				case 'text_small_value':
					$gen_field = "<textarea class='wiswigs' name='id_type_field[" . $v->id_type_field . "]'>{$v->$codeText}</textarea>";
					break;
			}
			$arr = (array)$v;
			$arr[ 'gen_field' ] = $gen_field;
			$valuesArr[ $v->id_type_field ] = $arr;
		}
		$order = "type.id_type ASC";
		$type = $sm->get( 'Application\Model\TypeTable' )->fetchAll( false, $order );
		$order = "menu.id_menu_main ASC";
		$menu = $sm->get( 'Application\Model\MenuTable' )->fetchAll( false, $order, false );
		$fetchMenuArray = array();
		foreach( $menu as $v ){
			$fetchMenuArray[ ] = (array)$v;
		}

		$menuAll = $this->genMenu( 0, $fetchMenuArray );

		$order = "foto.id_foto ASC";
		$where = "foto.id_contents = '$id_contents'";
		$foto = $sm->get( 'Application\Model\FotoTable' )->fetchAll( false, $order, $where );
		$order = "file.id_file ASC";
		$where = "file.id_contents = '$id_contents'";
		$file = $sm->get( 'Application\Model\FileTable' )->fetchAll( false, $order, $where );
		$categoryBrand = false;
		if(!empty($contents -> id_brand)){
			$where = "category_brand.id_brand = '{$contents -> id_brand}'";
			$order = "category.id_category ASC";
			$categoryBrand = $sm->get( 'Application\Model\CategoryBrandTable' )
				->joinCategory()
				->joinBrand()
				->joinMoneyCb($id_contents)
				->fetchAll( false, $order, $where );
		}

		return new ViewModel(
			array( 'type' => $type,
				'menu' => $menuAll,
				'contents' => $contents,
				'values' => $valuesArr,
				'foto' => $foto,
				'file' => $file,
				'categoryBrand' => $categoryBrand,
			) );
	}


	public function genMenu( $id_parent_menu, $menuAll, $t = 0 ){
		$menu = array();
		$t ++;
		foreach( $menuAll as &$v ){
			if( $id_parent_menu == $v[ 'id_menu_main' ] ){
				$padding = $t;
				for( $i = 0; $i <= ( $t - 1 ); $i ++ ){
					$padding += 15;
				}
				$menu[ $v[ 'id_menu' ] ] = $v;
				$menu[ $v[ 'id_menu' ] ][ 'labelMenuTire' ] = $v[ 'label_menu' ];
				$menu[ $v[ 'id_menu' ] ][ 'padding' ] = $padding;
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

	public function translitIt( $str ){
		$tr = array( "А" => "a", "Б" => "b", "В" => "v", "Г" => "g", "Д" => "d", "Е" => "e", "Ж" => "j", "З" => "z", "И" => "i", "Й" => "y", "К" => "k", "Л" => "l", "М" => "m", "Н" => "n", "О" => "o", "П" => "p", "Р" => "r", "С" => "s", "Т" => "t", "У" => "u", "Ф" => "f", "Х" => "h", "Ц" => "ts", "Ч" => "ch", "Ш" => "sh", "Щ" => "sch", "Ъ" => "", "Ы" => "yi", "Ь" => "", "Э" => "e", "Ю" => "yu", "Я" => "ya", "а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "j", "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l", "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r", "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h", "ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "y", "ы" => "yi", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya", " " => "_", "." => "", "/" => "_" );

		return strtr( $str, $tr );
	}

}
