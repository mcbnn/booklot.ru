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

class TemplateController extends AbstractActionController
{
	public function indexAction()
	{
        $sm=$this->getServiceLocator();
        $order="type.id_type DESC";
        $type=$sm->get('Application\Model\TypeTable')->fetchAll(false,$order,false);
		return new ViewModel(array('type'=>$type));
	}

	public function listSectionAction(){
		$sm=$this->getServiceLocator();
		$order="section.id_section DESC";
		$section=$sm->get('Application\Model\SectionTable')->fetchAll(false,$order,false);
		return new ViewModel(array('section'=>$section));
	}

	public function addSectionAction(){
		$sm=$this->getServiceLocator();
		$form = new SectionForm();
		$request = $this->getRequest();
		$update=$request->getPost('send');
		if(isset($update)){
			if ($request->isPost()) {
				$section = new Section();
				$form->setInputFilter($section->getInputFilter());
				$form->setData($request->getPost());
				if ($form->isValid()) {
					$section->exchangeArray($form->getData());
					$id_section=$sm->get('Application\Model\SectionTable')->save($section,false,false,true);
					$this->redirect()->toRoute('admin/slash/template',array('action'=>'listSection','subdomain'=>'admin'));
				}
			}
		}
		return new ViewModel(array('form'=>$form));
	}

	public function redactorSectionAction()
	{
		$sm=$this->getServiceLocator();
		$id_section=$this->params()->fromRoute('id');
		$form = new SectionForm();
		$request = $this->getRequest();
		$update=$request->getPost('send');
		$del=$request->getPost('del');
		if(isset($update)){
			if ($request->isPost()) {
				$section = new Section();
				$form->setInputFilter($section->getInputFilter());
				$form->setData($request->getPost());
				if ($form->isValid()) {
					$section->exchangeArray($form->getData());
					$sm->get('Application\Model\SectionTable')->save($section,$id_section,'id_section');
					$this->redirect()->toRoute('admin/slash/template',array('action'=>'listSection','subdomain'=>'admin'));
				}
			}
		}
		if(isset($del)){
			$sm->get('Application\Model\SectionTable')->delete('id_section',$id_section);
			$this->redirect()->toRoute('admin/slash/template',array('action'=>'listSection','subdomain'=>'admin'));
		}

		$where="section.id_section='$id_section'";
		$section=$sm->get('Application\Model\SectionTable')->fetchAll(false,false,$where);
		$section=$section->current();
		return new ViewModel(array('section'=>$section,'form'=>$form));
	}

	public function deleteTemplateAction(){
		$sm=$this->getServiceLocator();
		$id_type=$this->params()->fromRoute('id');
		$sm->get('Application\Model\TypeTable')->delete('id_type',$id_type);
		$sm->get('Application\Model\TypeFieldTable')->delete('id_type',$id_type);
		$this->redirect()->toRoute('admin/slash/template',array('action'=>'index','subdomain'=>'admin'));
	}

    public function addTemplateAction()
    {
        $sm=$this->getServiceLocator();
		$time=time();
        $request = $this->getRequest();
        $update=$request->getPost('send');
        if(isset($update)){
            if ($request->isPost()) {
				$arr=array();
				$arr['name_type']=$request->getPost('name_type');
	            $arr['site_content_type']=($request->getPost('site_content_type') == "on")?1:0;
				if(empty($arr['name_type'])){ $this->redirect()->toRoute('admin/slash/template',array('action'=>'addTemplate','subdomain'=>'admin'));}
				$arr['datetime_create']=date("Y-m-d H:i:s",$time);
				$id_type=$sm->get('Application\Model\TypeTable')->save($arr,false,false,true);
				$name_type_field=$request->getPost('name_type_field');
                $order_type_field=$request->getPost('order_type_field');
				$alias_type_field=$request->getPost('alias_type_field');
                $vis_type_field=$request->getPost('vis_type_field');
	            $vis2_type_field=$request->getPost('vis2_type_field');
				$id_section=$request->getPost('id_section');
				$id_field=$request->getPost('id_field');
				foreach($name_type_field as $k=>$v){
					$arr=array();
					if(!empty($v)){
						$arr['name_type_field']=$v;
                        $arr['order_type_field']=$order_type_field[$k];
						$arr['id_field']=$id_field[$k];
						$arr['id_type']=$id_type;
						$arr['alias_type_field']=$alias_type_field[$k];
						$arr['id_section']=$id_section[$k];
                        $arr['vis_type_field']=(!empty($vis_type_field[$k]))?1:0;
						$arr['vis2_type_field']=(!empty($vis2_type_field[$k]))?1:0;
						$sm->get('Application\Model\TypeFieldTable')->save($arr,false,false,true);
					}
				}
                $this->redirect()->toRoute('admin/slash/template',array('action'=>'index','subdomain'=>'admin'));
            }
        }
        $order="field.order_field ASC";
        $field=$sm->get('Application\Model\FieldTable')->fetchAll(false,$order);
		$order="section.order_section DESC";
		$section=$sm->get('Application\Model\SectionTable')->fetchAll(false,$order);
        return new ViewModel(array('field'=>$field,'section'=>$section));
    }

    public function redactorTemplateAction()
    {
		$sm=$this->getServiceLocator();
		$time=time();
		$request = $this->getRequest();
		$update=$request->getPost('send');
		$del=$request->getPost('del');
		$id_type=$this->params()->fromRoute('id');
		if(isset($update)){
			if ($request->isPost()) {
				$arr=array();
				$arr['name_type']=$request->getPost('name_type');
				$arr['site_content_type']=($request->getPost('site_content_type') == "on")?1:0;
				if(empty($arr['name_type'])){ $this->redirect()->toRoute('admin/slash/template',array('action'=>'addTemplate','subdomain'=>'admin'));}
				$arr['datetime_create']=date("Y-m-d H:i:s",$time);
				$sm->get('Application\Model\TypeTable')->save($arr, $id_type, 'id_type');
				$name_type_field=$request->getPost('name_type_field');
                $order_type_field=$request->getPost('order_type_field');
				$alias_type_field=$request->getPost('alias_type_field');
                $vis_type_field=$request->getPost('vis_type_field');
				$vis2_type_field=$request->getPost('vis2_type_field');
				$id_field=$request->getPost('id_field');
				$id_section=$request->getPost('id_section');
				$sm->get('Application\Model\TypeFieldTable')->delete('id_type',$id_type);
				foreach($name_type_field as $k=>$v){
					$arr=array();
					if(!empty($v)){
						$arr['id_type_field']=!strpos($k, '_')?$k:'';
						$arr['name_type_field']=$v;
                        $arr['order_type_field']=$order_type_field[$k];
						$arr['alias_type_field']=$alias_type_field[$k];
						$arr['id_field']=$id_field[$k];
						$arr['id_type']=$id_type;
						$arr['id_section']=$id_section[$k];
                        $arr['vis_type_field']=(!empty($vis_type_field[$k]))?1:0;
						$arr['vis2_type_field']=(!empty($vis2_type_field[$k]))?1:0;
						$sm->get('Application\Model\TypeFieldTable')->save($arr,false,false,true);
					}
				}
				$this->redirect()->toRoute('admin/slash/template',array('action'=>'index','subdomain'=>'admin'));
			}
		}
		if(isset($del)){
			$sm->get('Application\Model\TypeTable')->delete('id_type',$id_type);
			$sm->get('Application\Model\TypeFieldTable')->delete('id_type',$id_type);
			$this->redirect()->toRoute('admin/slash/template',array('action'=>'index','subdomain'=>'admin'));
		}
		$order="field.order_field ASC";
		$field=$sm->get('Application\Model\FieldTable')->fetchAll(false,$order);
		$where="type.id_type='$id_type'";
		$type=$sm->get('Application\Model\TypeTable')->fetchAll(false,false,$where);
		$type=$type->current();
		$where="type_field.id_type='$id_type'";
        $order="type_field.order_type_field DESC";
		$typeField=$sm->get('Application\Model\TypeFieldTable')->fetchAll(false,$order,$where);
		$order="section.order_section DESC";
		$section=$sm->get('Application\Model\SectionTable')->fetchAll(false,$order);
		return new ViewModel(array('field'=>$field,'type'=>$type,'typeField'=>$typeField,'section'=>$section));
    }

    public function genMenu($id_parent_menu,$menuAll,$t=0){
        $menu = array();
        $t++;
        foreach($menuAll as  &$v){
            if($id_parent_menu==$v['id_menu_main']){
                $tire="";
                for($i=0;$i<=($t-1);$i++){
                    $tire.="--";
                }
                $menu[$v['id_menu']]=$v;
                $menu[$v['id_menu']]['labelMenuTire']=$tire.$v['label_menu'];
                $children=$this->genMenu($v['id_menu'],$menuAll,$t);
                if(!empty($children)){
                    foreach($children as $b=>$n){
                        $menu[$b]=$n;
                    }
                }
            }
        }

        return $menu;
    }

}
