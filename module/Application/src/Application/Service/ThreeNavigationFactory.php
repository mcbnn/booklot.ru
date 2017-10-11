<?php

namespace Application\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Navigation\Service\DefaultNavigationFactory;

class ThreeNavigationFactory extends DefaultNavigationFactory
{
	protected function getPages(ServiceLocatorInterface $serviceLocator)
	{
		if (null === $this->pages) {
			$order="menu.order_menu DESC";
			$where="menu.vis_menu=1";
			$fetchMenu = $serviceLocator->get('Application\Model\MenuTable')->fetchAllMenu(false,$order,$where);

			$configuration = array();

			foreach($fetchMenu as $key=>$row){

				if(!empty($row->action_menu) and !empty($row->route_menu)){
					$configuration[$row->id_menu]= array(
						'id_menu' => $row->id_menu,
						'id_menu_main' => $row->id_menu_main,
						'vis_menu' => $row->vis_menu,
						'id_type_menu' => $row->id_type_menu,
						'vis_menu' => $row->vis_menu,
						'module_type_menu' => $row->module_type_menu,
						'label' => $row->label_menu,
						'route' => $row->route_menu,
						'class' => $row->class_type_menu,
						'action' => $row->action_menu,
						'id'=>$row->id_menu,
						'params' => array('id'=>$row->id_menu,'row'=>$row->action_menu),

					);
				}
				elseif(!empty($row->action_menu)){
					$configuration[$row->id_menu]= array(
						'id_menu' => $row->id_menu,
						'id_menu_main' => $row->id_menu_main,
						'vis_menu' => $row->vis_menu,
						'id_type_menu' => $row->id_type_menu,
						'vis_menu' => $row->vis_menu,
						'module_type_menu' => $row->module_type_menu,
						'label' => $row->label_menu,
						'class' => $row->class_type_menu,
						'action' => $row->action_menu,
						'id'=>$row->id_menu,
						'params' => array('id'=>$row->id_menu,'row'=>$row->action_menu),


					);
				}
				elseif(!empty($row->route_menu)){
					$configuration[$row->id_menu]= array(
						'id_menu' => $row->id_menu,
						'id_menu_main' => $row->id_menu_main,
						'vis_menu' => $row->vis_menu,
						'id_type_menu' => $row->id_type_menu,
						'vis_menu' => $row->vis_menu,
						'module_type_menu' => $row->module_type_menu,
						'label' => $row->label_menu,
						'route' => $row->route_menu,
						'class' => $row->class_type_menu,
						'id'=>$row->id_menu,
						'params' => array('id'=>$row->id_menu,'row'=>$row->action_menu),



					);
				}
				else{
					$configuration[$row->id_menu]= array(
						'id_menu' => $row->id_menu,
						'id_menu_main' => $row->id_menu_main,
						'vis_menu' => $row->vis_menu,
						'id_type_menu' => $row->id_type_menu,
						'vis_menu' => $row->vis_menu,
						'module_type_menu' => $row->module_type_menu,
						'label' => $row->label_menu,
						'class' => $row->class_type_menu,
						'params' => array('id'=>$row->id_menu,'row'=>$row->action_menu),
					);
				}
			}
			$arr['navigation'][$this->getName()]=$this->genMenu($configuration);
			$configuration='';
			$configuration=$arr;

//			print_r($configuration);
//			die();
			if (!isset($configuration['navigation'])) {
				throw new Exception\InvalidArgumentException('Could not find navigation configuration key');
			}
			if (!isset($configuration['navigation'][$this->getName()])) {
				throw new Exception\InvalidArgumentException(sprintf(
					'Failed to find a navigation container by the name "%s"',
					$this->getName()
				));
			}

			$application = $serviceLocator->get('Application');
			$routeMatch  = $application->getMvcEvent()->getRouteMatch();
			$router      = $application->getMvcEvent()->getRouter();
			$pages       = $this->getPagesFromConfig($configuration['navigation'][$this->getName()]);
			$this->pages = $this->injectComponents($pages, $routeMatch, $router);
		}

		return $this->pages;
	}

	protected function genMenu($arr,$id_menu_main=0){
		$key=array();
		foreach($arr as $v){
			if($v['id_menu_main']==$id_menu_main){
				$key[$v['id_menu']]=$v;
				$dop=$this->genMenu($arr,$v['id_menu']);
				if(!empty($dop)){
					$key[$v['id_menu']]['pages']=$dop;
				}

			}
		}
		return $key;

   }
}


?>