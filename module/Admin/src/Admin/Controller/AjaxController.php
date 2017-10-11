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
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Zend\Session\Container as Container;
use Zend\View\View;

class AjaxController extends AbstractActionController
{
    public function indexAction()
    {
        $id = $this->params()->fromRoute('id');
        switch ($id) {
            case "1":
                if ($template = $this->genTemplate()) {
                    return $template;
                };
                break;
            case "2":
                $this->searchBrand();
                break;
            case "3":
                $this->searchModel();
                break;
            case "4":
                if ($template = $this->genMenuContent()) {
                    return $template;
                };
                break;
            case "5":
                $this->searchContent();
                break;
			case "6":
				if ($template = $this->categoyForm()) {
					return $template;
				};
				break;
	        case "7":
		        if ($template = $this -> genTemplate2()) {
			        return $template;
		        };
		        break;
        }
        die();
    }

	public function categoyForm(){

		$sm = $this->getServiceLocator();
		$fromPost = $this -> params() -> fromPost();
		$id_brand = $fromPost['data']['id_brand'];
		if(!isset($fromPost['data']['id_brand']) or empty($fromPost['data']['id_brand'])) die();
		$where = "brand.id_brand = '$id_brand'";
		$order = "category.id_category ASC";
		$categoryBrand = $sm->get('Application\Model\CategoryBrandTable')
			->joinCategory()
			->joinBrand()
			->fetchAll(false,$order,$where);
		$view = new ViewModel(array('categoryBrand' => $categoryBrand));
		$view->setTemplate('admin/ajax/category');
		$view->setTerminal(true);
		return $view;
	}

    public function searchContent()
    {

        $sm = $this->getServiceLocator();
        $id_type_field = $this->params()->fromPost('id_type_field');
        $code = $this->params()->fromPost('rem');
        $search = $this->params()->fromPost('search');

        $where = "values.$code like '%$search%' and values.id_type_field = '$id_type_field'";
        $model = $sm->get('Application\Model\ValuesTable')->fetchAll(false, false, $where);
        $modelArray = array();
        foreach ($model as $k => $v) {
            $r=$v->$code;
            $modelArray[$r] = $r;
        }
        $gmass=array();
        foreach($modelArray as $b){
            $gmass['fantazy'][]=$b;
        }
        $gmass = \Zend\Json\Json::encode($gmass, true);
        print_r($gmass);
        die();
    }


    public function genMenuContent()
    {
        $sm = $this->getServiceLocator();
        $id_menu = $this->params()->fromPost('data');
        $order="menu.id_menu_main ASC";
        $menu=$sm->get('Application\Model\MenuTable')->joinType()->fetchAll(false,$order,false);
        $fetchMenuArray=array();
        foreach($menu as $v){
            $fetchMenuArray[]=(array)$v;
        }
        $menuAll=$this->genMenu(0,$fetchMenuArray);
        $where="menu.id_menu='$id_menu'";
        $menuSelf=$sm->get('Application\Model\MenuTable')->fetchAll(false,false,$where);
        $menuSelf=$menuSelf->current();
        $id_type=0;
        if(empty($menuSelf->id_type)){
            $typeCheck=$this->checkType($menuSelf->id_menu_main,$fetchMenuArray);
            $id_type=$typeCheck['id_type'];
        }
        else{
            $id_type=$menuSelf->id_type;
        }
	    print_r($id_type);
	    die();
        if(empty($id_type)){ echo 'error: Нет шаблона у этого меню'; die();}
        $where = "type_field.id_type='$id_type'";
        $order = "type_field.order_type_field DESC";
        $objTypeField = $sm->get('Application\Model\TypeFieldTable')->fetchAll(false, $order, $where);
        $arrTypeField = array();
        foreach ($objTypeField as $v) {
            $gen_field = "";
            switch ($v->code_field) {
                case 'varchar_value':
                    $gen_field = "<input id_type_field='".$v->id_type_field ."' rem='varchar_value' onkeyup='search_admin_content(this,event)' class='varchartext' name='id_type_field[" . $v->id_type_field . "]' type='text' />";
                    break;
                case 'text_value':
                    $gen_field = "<textarea id_type_field='".$v->id_type_field ."'   rem='text_value'  class='wiswigs' name='id_type_field[" . $v->id_type_field . "]'></textarea>";
                    break;
                case 'date_value':
                    $gen_field = '<div   class="input-append date datepicker"><input class="dateimecss" name="id_type_field[' . $v->id_type_field . ']" data-format="yyyy-MM-dd" type="text"></input><span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span></div>';
                    break;
                case 'datetime_value':
                    $gen_field = '<div   class="input-append date datetimepicker"><input class="dateimecss" name="id_type_field[' . $v->id_type_field . ']" data-format="yyyy-MM-dd hh:mm:00" type="text"></input><span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span></div>';
                    break;
                case 'int_value':
                    $gen_field = "<input id_type_field='".$v->id_type_field ."'   rem='int_value'  onkeyup='search_admin_content(this,event)'  class='integer' name='id_type_field[" . $v->id_type_field . "]' type='text' />";
                    break;
                case 'text_small_value':
                    $gen_field = "<textarea id_type_field='".$v->id_type_field ."'  rem='text_small_value'  class='wiswigs' name='id_type_field[" . $v->id_type_field . "]'></textarea>";
                    break;
            }
            $arr = (array)$v;
            $arr['gen_field'] = $gen_field;
            $arrTypeField[$v->id_type_field] = $arr;
        }
        $order = "menu.id_menu_main ASC";
        $menu = $sm->get('Application\Model\MenuTable')->fetchAll(false, $order, false);
        $fetchMenuArray = array();
        foreach ($menu as $v) {
            $fetchMenuArray[] = (array)$v;
        }

        $menuAll = $this->genMenu(0, $fetchMenuArray);
        $view = new ViewModel(array(
	        'typeField' => $arrTypeField,
	        'menu' => $menuAll));
		$view -> setTemplate('admin/ajax/addtemplate');
	    $view -> setTerminal(true);

//	    $jsonModel = new JsonModel();
//	    $jsonModel-> setVariables(array(
//		    'html' => $view,
//		    'jsonVar1' => 'jsonVal2',
//		    'jsonArray' => array(1,2,3,4,5,6)
//	    ));

//	    $jsonModel = new  JsonModel(array($view));
//	    $jsonModel -> setTemplate('admin/ajax/addtemplate');
//	    $jsonModel -> setVariables(array('typeField' => $arrTypeField,
//		    'menu' => $menuAll));
//	    $r = new Json();
//        return $jsonModel ;

	    $partial = $this->getServiceLocator()->get('viewhelpermanager')->get('partial');
	    return new JsonModel(array(
		    'html' => $partial('admin/ajax/addtemplate.phtml', array("key" => "value")),
		    'jsonVar1' => 'jsonVal2',
		    'jsonArray' => array(1, 2, 3, 4, 5, 6)
	    ));
    }

	public function genTemplate2(){
		$sm = $this->getServiceLocator();
		$id_type = $this->params()->fromPost('data');
		if(empty($id_type)){ echo 'error: Нет шаблона'; die();}
		$where = "type_field.id_type='$id_type'";
		$order = "type_field.order_type_field DESC";
		$objTypeField = $sm->get('Application\Model\TypeFieldTable')->fetchAll(false, $order, $where);
		$arrTypeField = array();
		foreach ($objTypeField as $v) {
			$gen_field = "";
			switch ($v->code_field) {
				case 'varchar_value':
					$gen_field = "<input id_type_field='".$v->id_type_field ."' rem='varchar_value' onkeyup='search_admin_content(this,event)' class='varchartext' name='id_type_field[" . $v->id_type_field . "]' type='text' />";
					break;
				case 'text_value':
					$gen_field = "<textarea id_type_field='".$v->id_type_field ."'   rem='text_value'  class='wiswigs' name='id_type_field[" . $v->id_type_field . "]'></textarea>";
					break;
				case 'date_value':
					$gen_field = '<div   class="input-append date datepicker"><input class="dateimecss" name="id_type_field[' . $v->id_type_field . ']" data-format="yyyy-MM-dd" type="text"></input><span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span></div>';
					break;
				case 'datetime_value':
					$gen_field = '<div   class="input-append date datetimepicker"><input class="dateimecss" name="id_type_field[' . $v->id_type_field . ']" data-format="yyyy-MM-dd hh:mm:00" type="text"></input><span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span></div>';
					break;
				case 'int_value':
					$gen_field = "<input id_type_field='".$v->id_type_field ."'   rem='int_value'  onkeyup='search_admin_content(this,event)'  class='integer' name='id_type_field[" . $v->id_type_field . "]' type='text' />";
					break;
				case 'text_small_value':
					$gen_field = "<textarea id_type_field='".$v->id_type_field ."'  rem='text_small_value'  class='wiswigs' name='id_type_field[" . $v->id_type_field . "]'></textarea>";
					break;
			}
			$arr = (array)$v;
			$arr['gen_field'] = $gen_field;
			$arrTypeField[$v->id_type_field] = $arr;
		}
		$order = "menu.id_menu_main ASC";
		$menu = $sm->get('Application\Model\MenuTable')->fetchAll(false, $order, false);
		$fetchMenuArray = array();
		foreach ($menu as $v) {
			$fetchMenuArray[] = (array)$v;
		}

		$menuAll = $this->genMenu(0, $fetchMenuArray);
		$view = new ViewModel(array(
			'typeField' => $arrTypeField,
			'menu' => $menuAll));
		$view -> setTemplate('admin/ajax/addtemplate');
		$view -> setTerminal(true);
        return $view;
	}



    public function searchBrand()
    {
        $sm = $this->getServiceLocator();
        $order = "brand.order_brand DESC,brand.name_brand ASC";
        $search = $this->params()->fromPost('search');
        $where = "brand.name_brand like '$search%'";
        $brand = $sm->get('Application\Model\BrandTable')->fetchAll(false, $order, $where);
        $brandArray = array();
        foreach ($brand as $k => $v) {
            $brandArray[] = $v;
        }
        $jsonObject = \Zend\Json\Json::encode($brandArray, true);
        print_r($jsonObject);
        die();
    }

    public function searchModel()
    {
        $sm = $this->getServiceLocator();
        $order = "model.order_model DESC,model.name_model ASC";
        $search = $this->params()->fromPost('search');
        $where = "model.name_model like '$search%'";
        $model = $sm->get('Application\Model\ModelTable')->fetchAll(false, $order, $where);
        $modelArray = array();
        foreach ($model as $k => $v) {
            $modelArray[] = $v;
        }
        $jsonObject = \Zend\Json\Json::encode($modelArray, true);
        print_r($jsonObject);
        die();
    }

    public function genTemplate()
    {
        $sm = $this->getServiceLocator();
        $id_type = $this->params()->fromPost('data');

        $where = "type_field.id_type='$id_type'";
        $order = "type_field.order_type_field DESC";
        $objTypeField = $sm->get('Application\Model\TypeFieldTable')->fetchAll(false, $order, $where);
        $arrTypeField = array();
        foreach ($objTypeField as $v) {
            $gen_field = "";
            switch ($v->code_field) {
                case 'varchar_value':
                    $gen_field = "<input class='varchartext' name='id_type_field[" . $v->id_type_field . "]' type='text' />";
                    break;
                case 'text_value':
                    $gen_field = "<textarea class='wiswigs' name='id_type_field[" . $v->id_type_field . "]'></textarea>";
                    break;
                case 'date_value':
                    $gen_field = '<div   class="input-append date datepicker"><input class="dateimecss" name="id_type_field[' . $v->id_type_field . ']" data-format="yyyy-MM-dd" type="text"></input><span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span></div>';
                    break;
                case 'datetime_value':
                    $gen_field = '<div   class="input-append date datetimepicker"><input class="dateimecss" name="id_type_field[' . $v->id_type_field . ']" data-format="yyyy-MM-dd hh:mm:00" type="text"></input><span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span></div>';
                    break;
                case 'int_value':
                    $gen_field = "<input class='integer' name='id_type_field[" . $v->id_type_field . "]' type='text' />";
                    break;
                case 'text_small_value':
                    $gen_field = "<textarea class='wiswigs' name='id_type_field[" . $v->id_type_field . "]'></textarea>";
                    break;
            }
            $arr = (array)$v;
            $arr['gen_field'] = $gen_field;
            $arrTypeField[$v->id_type_field] = $arr;
        }
        $order = "menu.id_menu_main ASC";
        $menu = $sm->get('Application\Model\MenuTable')->fetchAll(false, $order, false);
        $fetchMenuArray = array();
        foreach ($menu as $v) {
            $fetchMenuArray[] = (array)$v;
        }

        $menuAll = $this->genMenu(0, $fetchMenuArray);
        $view = new ViewModel(array('typeField' => $arrTypeField, 'menu' => $menuAll));
        $view->setTemplate('admin/ajax/addtemplate');
        $view->setTerminal(true);
        return $view;

    }

    public function genMenu($id_parent_menu, $menuAll, $t = 0)
    {
        $menu = array();
        $t++;
        foreach ($menuAll as &$v) {
            if ($id_parent_menu == $v['id_menu_main']) {
                $tire = "";
                for ($i = 0; $i <= ($t - 1); $i++) {
                    $tire .= "--";
                }
                $menu[$v['id_menu']] = $v;
                $menu[$v['id_menu']]['labelMenuTire'] = $tire . $v['label_menu'];
                $children = $this->genMenu($v['id_menu'], $menuAll, $t);
                if (!empty($children)) {
                    foreach ($children as $b => $n) {
                        $menu[$b] = $n;
                    }
                }
            }
        }

        return $menu;
    }

    public function checkType($id_parent_menu,$menuAll){

        foreach($menuAll as $v){
            if($id_parent_menu==$v['id_menu']){
                if(!empty($v['id_type'])){
                    return $v;
                }
                $children=$this->checkType($v['id_menu_main'],$menuAll);
                if(!empty($children)){
                    return $children;
                }
            }
        }
        return false;
    }


}
