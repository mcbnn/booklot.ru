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

use Zend\Session\Container as Container;


class ParserController extends AbstractActionController{

	public $folders;

	public function indexAction(){
			die();
			header('Content-Type: text/html; charset=utf-8');
			$id = $this -> params() -> fromRoute('id');
			$id = 5;
			switch($id){
				case '1' : $this -> Borovichi_myagkaya_mebeli();
					break;
				case '2' : $this -> Akvilon();
					break;
				case '3' : $this -> Elegeya();
					break;
				case '4' : $this -> Mebelinika();
					break;
				case '5' : $this ->  Mebelinii_dvor();
					break;

			}
			print_r($id);
			die();

	}

	public function Mebelinii_dvor(){

		$id_brand = 47; //аквилон
		$id_type = 20;
		$obem = 109; //объем
		$ves = 108; //вес
		$vysota = 103; //высота
		$glubina = 104; //глубина
		$shirina = 102; //ширина
		$opisanie = 107; //описание
		$dlinaspalnogo = 106; //длина спального места
		$shirinaspalnogo = 105; //ширина спкального места
		$id_moderator = 5;
		$id_user = 3;

		$sm = $this->getServiceLocator();
		$this -> folders = 'mebelinii_dvor';

		$dir = $_SERVER['DOCUMENT_ROOT'] . "/parser/".$this -> folders;;
		$file = $dir .'/'.'tmp.html';
		$contents = file_get_contents($file);
		//$contents = preg_replace('/src="(.*)"/', '' ,$contents);
		preg_match_all('/<tr.*>(.*)<\/tr>/isuU',  $contents, $m  );

//				print_r($m);
//				print_r($contents);
//				die();
		foreach($m[1] as $k => $v){

			if($k < 0)continue;
			preg_match_all('/<td.*>(.*)<\/td>
			/xisU',  $v, $td  );
			$count = count($td[1]);
			//if($count!=5)continue;
			$arr = array();
			foreach($td[1] as $b){
				$arr[] = $this -> clearStr($b);
			}
			$money = (int)preg_replace('/[\s]*/isU',"",$arr[4]);
//			print_r($arr);
//				print_r($money);
//				die();
			$arrSave = array();

			$shirinaZ = false;
			$glubinaZ = false;
			$vysotaZ = false;
			$shirinaspalnogoZ = false;
			$dlinaspalnogoZ = false;
			if(empty($money)) continue;
			$exp = explode("*",$arr[2]);
			if(isset($exp[0])){
				$vysotaZ = $exp[0];
			}
			if(isset($exp[1])){
				$shirinaZ = $exp[1];
			}
			if(isset($exp[2])){
				$glubinaZ = $exp[2];
			}
			$opisanieZ = $arr[3];

			$arrSave['id_menu'] = 0;
			$arrSave['name_contents'] = $arr[1];
			$arrSave['money_contents'] =$money;
			$arrSave['id_brand'] = $id_brand;
			$arrSave['name_parser_contents'] = $arr[1];
			$arrSave['alias_contents'] = $this -> translitIt($arr[1]);
			$arrSave['alias_contents'] = preg_replace('/(_{2,})/isU', '_', $arrSave['alias_contents']);
			$arrSave['datetime_create'] = date("Y-m-d H:i:s");
			$arrSave['id_type'] = $id_type;
			$arrSave['vis_contents'] = 0;
			$arrSave['id_moderator'] = $id_moderator;
			$arrSave['id_users'] = $id_user;
			$where = "contents.name_parser_contents = '".$arr[1]."'";
			$check = $sm->get( 'Application\Model\ContentsTable' ) -> fetchAll( false, false, $where );
			if($check -> count() == 0){
				$id_contents = $sm->get( 'Application\Model\ContentsTable' )->save( $arrSave,false, false, true );
			}
			else{
				$id_contents = $check -> current();
				$id_contents = $id_contents -> id_contents;
				$arrT['money_contents'] = $arrSave['money_contents'];
				$id_contents = $sm->get( 'Application\Model\ContentsTable' )->save( $arrT, $id_contents, 'id_contents',true);
			}
			if(empty($id_contents)) continue;

			if(!empty($shirinaZ)){
				//ширина
				$where = "values.id_type_field = '$shirina' and values.id_contents = '$id_contents'";
				$check = $sm->get( 'Application\Model\ValuesTable' ) -> fetchAll( false, false, $where );
				if($check -> count() == 0){
					$arrSave =array();
					$arrSave['id_type_field'] = $shirina;
					$arrSave['id_contents'] = $id_contents;
					$arrSave['varchar_value'] = $shirinaZ;
					$sm->get( 'Application\Model\ValuesTable' )->save( $arrSave,false, false, true );
				}
				else{
					$arrSave =array();
					$arrSave['id_type_field'] = $shirina;
					$arrSave['id_contents'] = $id_contents;
					$arrSave['varchar_value'] = $shirinaZ;
					$where = "values.id_type_field = '$shirina' and values.id_contents = '$id_contents'";
					$sm->get( 'Application\Model\ValuesTable' ) -> updateArr( $arrSave, $where );
				}
			}

			if(!empty($glubinaZ)){
				//глубина
				$where = "values.id_type_field = '$glubina' and values.id_contents = '$id_contents'";
				$check = $sm->get( 'Application\Model\ValuesTable' ) -> fetchAll( false, false, $where );
				if($check -> count() == 0){
					$arrSave =array();
					$arrSave['id_type_field'] = $glubina;
					$arrSave['id_contents'] = $id_contents;
					$arrSave['varchar_value'] = $glubinaZ;
					$sm->get( 'Application\Model\ValuesTable' )->save( $arrSave,false, false, true );
				}
				else{
					$arrSave =array();
					$arrSave['id_type_field'] = $glubina;
					$arrSave['id_contents'] = $id_contents;
					$arrSave['varchar_value'] = $glubinaZ;
					$where = "values.id_type_field = '$glubina' and values.id_contents = '$id_contents'";
					$sm->get( 'Application\Model\ValuesTable' ) -> updateArr( $arrSave, $where );
				}
			}


			if(!empty($vysotaZ)){
				//высота
				$where = "values.id_type_field = '$vysota' and values.id_contents = '$id_contents'";
				$check = $sm->get( 'Application\Model\ValuesTable' ) -> fetchAll( false, false, $where );
				if($check -> count() == 0){
					$arrSave =array();
					$arrSave['id_type_field'] = $vysota;
					$arrSave['id_contents'] = $id_contents;
					$arrSave['varchar_value'] = $vysotaZ;
					$sm->get( 'Application\Model\ValuesTable' )->save( $arrSave,false, false, true );
				}
				else{
					$arrSave =array();
					$arrSave['id_type_field'] = $vysota;
					$arrSave['id_contents'] = $id_contents;
					$arrSave['varchar_value'] = $vysotaZ;
					$where = "values.id_type_field = '$vysota' and values.id_contents = '$id_contents'";
					$sm->get( 'Application\Model\ValuesTable' ) -> updateArr( $arrSave, $where );
				}
			}
			if(!empty($opisanieZ)){
				//описание
				$where = "values.id_type_field = '$opisanie' and values.id_contents = '$id_contents'";
				$check = $sm->get( 'Application\Model\ValuesTable' ) -> fetchAll( false, false, $where );
				if($check -> count() == 0){
					$arrSave =array();
					$arrSave['id_type_field'] = $opisanie;
					$arrSave['id_contents'] = $id_contents;
					$arrSave['text_small_value'] = $opisanieZ;
					$sm->get( 'Application\Model\ValuesTable' )->save( $arrSave,false, false, true );
				}
				else{
					$arrSave =array();
					$arrSave['id_type_field'] = $opisanie;
					$arrSave['id_contents'] = $id_contents;
					$arrSave['text_small_value'] = $opisanieZ;
					$where = "values.id_type_field = '$opisanie' and values.id_contents = '$id_contents'";
					$sm->get( 'Application\Model\ValuesTable' ) -> updateArr( $arrSave, $where );
				}
			}
		}
		die();
	}

	public function Mebelinika(){
		die();
		$id_brand = 55; //аквилон
		$id_type = 20;
		$obem = 109; //объем
		$ves = 108; //вес
		$vysota = 103; //высота
		$glubina = 104; //глубина
		$shirina = 102; //ширина
		$opisanie = 107; //описание
		$dlinaspalnogo = 106; //длина спального места
		$shirinaspalnogo = 105; //ширина спкального места
		$id_moderator = 5;
		$id_user = 3;

		$sm = $this->getServiceLocator();
		$this -> folders = 'Mebelinika';

		$dir = $_SERVER['DOCUMENT_ROOT'] . "/parser/".$this -> folders;;
		$file = $dir .'/'.'tmp.html';
		$contents = file_get_contents($file);
		//$contents = preg_replace('/src="(.*)"/', '' ,$contents);
		preg_match_all('/<tr.*>(.*)<\/tr>/isuU',  $contents, $m  );

//				print_r($m);
//				print_r($contents);
//				die();
		foreach($m[1] as $k => $v){

			if($k < 1)continue;
			preg_match_all('/<td.*>(.*)<\/td>
			/xisU',  $v, $td  );
			$count = count($td[1]);
			$arr = array();
			foreach($td[1] as $b){
				$arr[] = $this -> clearStr($b);
			}
			//print_r($arr);die();
			$arrSave = array();

			$shirinaZ = false;
			$glubinaZ = false;
			$vysotaZ = false;
			$shirinaspalnogoZ = false;
			$dlinaspalnogoZ = false;
			if(!is_numeric($arr[5])) continue;
			if(preg_match_all('/(^|[\s]{1})([0-9]*)[\s]*\х[\s]*([0-9]*).*\,[\s]*\В[\s]*([0-9]*)([\s]{1}|$)/',$arr[2],$_d)){
				$shirinaZ = $_d[2][0];
				$glubinaZ = $_d[3][0];
				$vysotaZ = $_d[4][0];
			};
			if(preg_match_all('/(^|[\s]{1})([0-9]*?)[\s]*\х[\s]{1,}([0-9]{1,}?)[\s]*?\,[\s]*?\В[\s]*?([0-9]*?)[\s]{1}.*спальное[\s].*([0-9]*?)[\s]{1,}х[\s]{1,}([0-9]*?)[\s]*([\s]{1}|\)|$)/isuU',$arr[2],$_d)){
				$shirinaZ = $_d[2][0];
				$glubinaZ = $_d[3][0];
				$vysotaZ = $_d[4][0];
				$shirinaspalnogoZ= $_d[5][0];
				$dlinaspalnogoZ= $_d[6][0];
			};

			$arrSave['id_menu'] = 0;
			$arrSave['name_contents'] = $arr[1];
			$arrSave['money_contents'] = (!empty($arr[5]))?$arr[5]:0;
			$arrSave['id_brand'] = $id_brand;
			$arrSave['name_parser_contents'] = $arr[1];
			$arrSave['alias_contents'] = $this -> translitIt($arr[1]);
			$arrSave['alias_contents'] = preg_replace('/(_{2,})/isU', '_', $arrSave['alias_contents']);
			$arrSave['datetime_create'] = date("Y-m-d H:i:s");
			$arrSave['id_type'] = $id_type;
			$arrSave['vis_contents'] = 0;
			$arrSave['id_moderator'] = $id_moderator;
			$arrSave['id_users'] = $id_user;
			$where = "contents.name_parser_contents = '".$arr[1]."'";

			$check = $sm->get( 'Application\Model\ContentsTable' ) -> fetchAll( false, false, $where );
			if($check -> count() == 0){
				$id_contents = $sm->get( 'Application\Model\ContentsTable' )->save( $arrSave,false, false, true );
			}
			else{
				$id_contents = $check -> current();
				$id_contents = $id_contents -> id_contents;
				$arrT['money_contents'] = $arrSave['money_contents'];
				$id_contents = $sm->get( 'Application\Model\ContentsTable' )->save( $arrT, $id_contents, 'id_contents',true);
			}
			if(empty($id_contents)) continue;
			//объем
			$where = "values.id_type_field = '$obem' and values.id_contents = '$id_contents'";
			$check = $sm->get( 'Application\Model\ValuesTable' ) -> fetchAll( false, false, $where );
			if($check -> count() == 0){
				$arrSave =array();
				$arrSave['id_type_field'] = $obem;
				$arrSave['id_contents'] = $id_contents;
				$arrSave['varchar_value'] = $arr[4];
				$sm->get( 'Application\Model\ValuesTable' )->save( $arrSave,false, false, true );
			}
			else{
				$arrSave =array();
				$arrSave['id_type_field'] = $obem;
				$arrSave['id_contents'] = $id_contents;
				$arrSave['varchar_value'] = $arr[4];
				$where = "values.id_type_field = '$obem' and values.id_contents = '$id_contents'";
				$sm->get( 'Application\Model\ValuesTable' ) -> updateArr( $arrSave, $where );
			}


			//вес
			$where = "values.id_type_field = '$ves' and values.id_contents = '$id_contents'";
			$check = $sm->get( 'Application\Model\ValuesTable' ) -> fetchAll( false, false, $where );
			if($check -> count() == 0){
				$arrSave =array();
				$arrSave['id_type_field'] = $ves;
				$arrSave['id_contents'] = $id_contents;
				$arrSave['varchar_value'] = $arr[3];
				$sm->get( 'Application\Model\ValuesTable' )->save( $arrSave,false, false, true );
			}
			else{
				$arrSave =array();
				$arrSave['id_type_field'] = $ves;
				$arrSave['id_contents'] = $id_contents;
				$arrSave['varchar_value'] = $arr[3];
				$where = "values.id_type_field = '$ves' and values.id_contents = '$id_contents'";
				$sm->get( 'Application\Model\ValuesTable' ) -> updateArr( $arrSave, $where );
			}

			if(!empty($shirinaZ)){
				//ширина
				$where = "values.id_type_field = '$shirina' and values.id_contents = '$id_contents'";
				$check = $sm->get( 'Application\Model\ValuesTable' ) -> fetchAll( false, false, $where );
				if($check -> count() == 0){
					$arrSave =array();
					$arrSave['id_type_field'] = $shirina;
					$arrSave['id_contents'] = $id_contents;
					$arrSave['varchar_value'] = $shirinaZ;
					$sm->get( 'Application\Model\ValuesTable' )->save( $arrSave,false, false, true );
				}
				else{
					$arrSave =array();
					$arrSave['id_type_field'] = $shirina;
					$arrSave['id_contents'] = $id_contents;
					$arrSave['varchar_value'] = $shirinaZ;
					$where = "values.id_type_field = '$shirina' and values.id_contents = '$id_contents'";
					$sm->get( 'Application\Model\ValuesTable' ) -> updateArr( $arrSave, $where );
				}
			}

			if(!empty($glubinaZ)){
				//глубина
				$where = "values.id_type_field = '$glubina' and values.id_contents = '$id_contents'";
				$check = $sm->get( 'Application\Model\ValuesTable' ) -> fetchAll( false, false, $where );
				if($check -> count() == 0){
					$arrSave =array();
					$arrSave['id_type_field'] = $glubina;
					$arrSave['id_contents'] = $id_contents;
					$arrSave['varchar_value'] = $glubinaZ;
					$sm->get( 'Application\Model\ValuesTable' )->save( $arrSave,false, false, true );
				}
				else{
					$arrSave =array();
					$arrSave['id_type_field'] = $glubina;
					$arrSave['id_contents'] = $id_contents;
					$arrSave['varchar_value'] = $glubinaZ;
					$where = "values.id_type_field = '$glubina' and values.id_contents = '$id_contents'";
					$sm->get( 'Application\Model\ValuesTable' ) -> updateArr( $arrSave, $where );
				}
			}


			if(!empty($vysotaZ)){
				//высота
				$where = "values.id_type_field = '$vysota' and values.id_contents = '$id_contents'";
				$check = $sm->get( 'Application\Model\ValuesTable' ) -> fetchAll( false, false, $where );
				if($check -> count() == 0){
					$arrSave =array();
					$arrSave['id_type_field'] = $vysota;
					$arrSave['id_contents'] = $id_contents;
					$arrSave['varchar_value'] = $vysotaZ;
					$sm->get( 'Application\Model\ValuesTable' )->save( $arrSave,false, false, true );
				}
				else{
					$arrSave =array();
					$arrSave['id_type_field'] = $vysota;
					$arrSave['id_contents'] = $id_contents;
					$arrSave['varchar_value'] = $vysotaZ;
					$where = "values.id_type_field = '$vysota' and values.id_contents = '$id_contents'";
					$sm->get( 'Application\Model\ValuesTable' ) -> updateArr( $arrSave, $where );
				}
			}


			if(!empty($shirinaspalnogoZ)){
				//ширина спального места
				$where = "values.id_type_field = '$shirinaspalnogo' and values.id_contents = '$id_contents'";
				$check = $sm->get( 'Application\Model\ValuesTable' ) -> fetchAll( false, false, $where );
				if($check -> count() == 0){
					$arrSave =array();
					$arrSave['id_type_field'] = $shirinaspalnogo;
					$arrSave['id_contents'] = $id_contents;
					$arrSave['varchar_value'] = $shirinaspalnogoZ;
					$sm->get( 'Application\Model\ValuesTable' )->save( $arrSave,false, false, true );
				}
				else{
					$arrSave =array();
					$arrSave['id_type_field'] = $shirinaspalnogo;
					$arrSave['id_contents'] = $id_contents;
					$arrSave['varchar_value'] = $shirinaspalnogoZ;
					$where = "values.id_type_field = '$shirinaspalnogo' and values.id_contents = '$id_contents'";
					$sm->get( 'Application\Model\ValuesTable' ) -> updateArr( $arrSave, $where );
				}
			}

			if(!empty($dlinaspalnogoZ)){
				//длина спального места
				$where = "values.id_type_field = '$dlinaspalnogo' and values.id_contents = '$id_contents'";
				$check = $sm->get( 'Application\Model\ValuesTable' ) -> fetchAll( false, false, $where );
				if($check -> count() == 0){
					$arrSave =array();
					$arrSave['id_type_field'] = $dlinaspalnogo;
					$arrSave['id_contents'] = $id_contents;
					$arrSave['varchar_value'] = $dlinaspalnogoZ;
					$sm->get( 'Application\Model\ValuesTable' )->save( $arrSave,false, false, true );
				}
				else{
					$arrSave =array();
					$arrSave['id_type_field'] = $dlinaspalnogo;
					$arrSave['id_contents'] = $id_contents;
					$arrSave['varchar_value'] = $dlinaspalnogoZ;
					$where = "values.id_type_field = '$dlinaspalnogo' and values.id_contents = '$id_contents'";
					$sm->get( 'Application\Model\ValuesTable' ) -> updateArr( $arrSave, $where );
				}
			}
		}
	 die();
	}


	public function Elegeya(){
		die();
		$id_brand = 6; //аквилон
		$id_type = 20;
		$id_type_field = 108;
		$sm = $this->getServiceLocator();
		$this -> folders = 'Elegeya';

		$dir = $_SERVER['DOCUMENT_ROOT'] . "/parser/".$this -> folders;;
		$file = $dir .'/'.'tmp.html';
		$contents = file_get_contents($file);

		//$contents = preg_replace('/src="(.*)"/', '' ,$contents);
		preg_match_all('/<tr.*>(.*)<\/tr>/isuU',  $contents, $m  );

//		print_r($m);
		print_r($contents);
//		die();
		foreach($m[1] as $k => $v){
			if($k < 3) continue;
			preg_match_all('/<td.*>(.*)<\/td>
			/xisU',  $v, $td  );

			$count = count($td[1]);
			$arr = array();
			foreach($td[1] as $b){
				$arr[] = $this -> clearStr($b);
			}

			$arrSave = array();
			$arrHelp = $arr;

			//print_r($arrHelp);die();
			$arr = array();
			$arr = $arrHelp;
			if(!is_numeric($arrHelp[1])) continue;
			if(!is_numeric($arrHelp[2])) continue;

			$arrSave['id_menu'] = 0;
			$arrSave['name_contents'] = $arr[0];
			$arrSave['money_contents'] = (!empty($arrHelp[1]))?$arrHelp[1]:0;
			$arrSave['id_brand'] = $id_brand;
			$arrSave['name_parser_contents'] = $arrHelp[0];
			$arrSave['alias_contents'] = $this -> translitIt($arrHelp[0]);
			$arrSave['alias_contents'] = preg_replace('/(_{2,})/isU', '_', $arrSave['alias_contents']);
			$arrSave['datetime_create'] = date("Y-m-d H:i:s");
			$arrSave['id_type'] = $id_type;
			$arrSave['vis_contents'] = 0;
			$arrSave['id_moderator'] = 6;
			$where = "contents.name_parser_contents = '".$arr[0]."'";
			$check = $sm->get( 'Application\Model\ContentsTable' ) -> fetchAll( false, false, $where );

			if($check -> count() == 0){
				$id_contents = $sm->get( 'Application\Model\ContentsTable' )->save( $arrSave,false, false, true );
			}
			else{
				$id_contents = $check -> current();
				$id_contents = $id_contents -> id_contents;
				$arrT['money_contents'] = $arrSave['money_contents'];
				$id_contents = $sm->get( 'Application\Model\ContentsTable' )->save( $arrT, $id_contents, 'id_contents',true);
			}
			if(empty($id_contents)) continue;
			//категория 1
			$arrSave = array();
			$arrSave['id_category'] = 1;
			$arrSave['id_brand'] = $id_brand;
			$where = "category_brand.id_category = '{$arrSave['id_category']}' and category_brand.id_brand = '$id_brand'";
			$check = $sm->get( 'Application\Model\CategoryBrandTable' ) -> fetchAll(false, false, $where);
			if($check -> count() == 0){
				$id_cb = $sm->get( 'Application\Model\CategoryBrandTable' )->save( $arrSave,false, false, true );
			}
			else{
				$id_cb = $check -> current();
				$id_cb = $id_cb -> id_cb;
			}
			$arrSave = array();
			$arrSave['id_cb'] = $id_cb;
			$arrSave['id_contents'] = $id_contents;
			$arrSave['money_cb'] = (empty($arr[1]))?0:$arr[1];
			$where = "money_cb.id_cb = '$id_cb' and money_cb.id_contents = '$id_contents'";
			$check = $sm->get( 'Application\Model\MoneyCbTable' ) -> fetchAll( false, false, $where );
			if($check -> count() == 0){
				$sm->get( 'Application\Model\MoneyCbTable' )->save( $arrSave,false, false, true );
			}
			else{
				$arrUpdate['id_cb'] = $id_cb;
				$arrUpdate['id_contents'] = $id_contents;
				$sm->get( 'Application\Model\MoneyCbTable' )->update($arrSave, $arrUpdate);
			}


			//категория 2
			$arrSave = array();
			$arrSave['id_category'] = 2;
			$arrSave['id_brand'] = $id_brand;
			$where = "category_brand.id_category = '{$arrSave['id_category']}' and category_brand.id_brand = '$id_brand'";
			$check = $sm->get( 'Application\Model\CategoryBrandTable' ) -> fetchAll(false, false, $where);
			if($check -> count() == 0){
				$id_cb = $sm->get( 'Application\Model\CategoryBrandTable' )->save( $arrSave,false, false, true );
			}
			else{
				$id_cb = $check -> current();
				$id_cb = $id_cb -> id_cb;
			}
			$arrSave = array();
			$arrSave['id_cb'] = $id_cb;
			$arrSave['id_contents'] = $id_contents;
			$arrSave['money_cb'] = (empty($arr[2]))?0:$arr[2];
			$where = "money_cb.id_cb = '$id_cb' and money_cb.id_contents = '$id_contents'";
			$check = $sm->get( 'Application\Model\MoneyCbTable' ) -> fetchAll( false, false, $where );
			if($check -> count() == 0){
				$sm->get( 'Application\Model\MoneyCbTable' )->save( $arrSave,false, false, true );
			}
			else{
				$arrUpdate['id_cb'] = $id_cb;
				$arrUpdate['id_contents'] = $id_contents;
				$sm->get( 'Application\Model\MoneyCbTable' )->update($arrSave, $arrUpdate);
			}


			//категория 3
			$arrSave = array();
			$arrSave['id_category'] = 3;
			$arrSave['id_brand'] = $id_brand;
			$where = "category_brand.id_category = '{$arrSave['id_category']}' and category_brand.id_brand = '$id_brand'";
			$check = $sm->get( 'Application\Model\CategoryBrandTable' ) -> fetchAll(false, false, $where);
			if($check -> count() == 0){
				$id_cb = $sm->get( 'Application\Model\CategoryBrandTable' )->save( $arrSave,false, false, true );
			}
			else{
				$id_cb = $check -> current();
				$id_cb = $id_cb -> id_cb;
			}
			$arrSave = array();
			$arrSave['id_cb'] = $id_cb;
			$arrSave['id_contents'] = $id_contents;
			$arrSave['money_cb'] = (empty($arr[3]))?0:$arr[3];
			$where = "money_cb.id_cb = '$id_cb' and money_cb.id_contents = '$id_contents'";
			$check = $sm->get( 'Application\Model\MoneyCbTable' ) -> fetchAll( false, false, $where );
			if($check -> count() == 0){
				$sm->get( 'Application\Model\MoneyCbTable' )->save( $arrSave,false, false, true );
			}
			else{
				$arrUpdate['id_cb'] = $id_cb;
				$arrUpdate['id_contents'] = $id_contents;
				$sm->get( 'Application\Model\MoneyCbTable' )->update($arrSave, $arrUpdate);
			}


			//категория 4
			$arrSave = array();
			$arrSave['id_category'] = 4;
			$arrSave['id_brand'] = $id_brand;
			$where = "category_brand.id_category = '{$arrSave['id_category']}' and category_brand.id_brand = '$id_brand'";
			$check = $sm->get( 'Application\Model\CategoryBrandTable' ) -> fetchAll(false, false, $where);
			if($check -> count() == 0){
				$id_cb = $sm->get( 'Application\Model\CategoryBrandTable' )->save( $arrSave,false, false, true );
			}
			else{
				$id_cb = $check -> current();
				$id_cb = $id_cb -> id_cb;
			}
			$arrSave = array();
			$arrSave['id_cb'] = $id_cb;
			$arrSave['id_contents'] = $id_contents;
			$arrSave['money_cb'] = (empty($arr[4]))?0:$arr[4];
			$where = "money_cb.id_cb = '$id_cb' and money_cb.id_contents = '$id_contents'";
			$check = $sm->get( 'Application\Model\MoneyCbTable' ) -> fetchAll( false, false, $where );
			if($check -> count() == 0){
				$sm->get( 'Application\Model\MoneyCbTable' )->save( $arrSave,false, false, true );
			}
			else{
				$arrUpdate['id_cb'] = $id_cb;
				$arrUpdate['id_contents'] = $id_contents;
				$sm->get( 'Application\Model\MoneyCbTable' )->update($arrSave, $arrUpdate);
			}



			//категория 5
			$arrSave = array();
			$arrSave['id_category'] = 5;
			$arrSave['id_brand'] = $id_brand;
			$where = "category_brand.id_category = '{$arrSave['id_category']}' and category_brand.id_brand = '$id_brand'";
			$check = $sm->get( 'Application\Model\CategoryBrandTable' ) -> fetchAll(false, false, $where);
			if($check -> count() == 0){
				$id_cb = $sm->get( 'Application\Model\CategoryBrandTable' )->save( $arrSave,false, false, true );
			}
			else{
				$id_cb = $check -> current();
				$id_cb = $id_cb -> id_cb;
			}
			$arrSave = array();
			$arrSave['id_cb'] = $id_cb;
			$arrSave['id_contents'] = $id_contents;
			$arrSave['money_cb'] = (empty($arr[5]))?0:$arr[5];
			$where = "money_cb.id_cb = '$id_cb' and money_cb.id_contents = '$id_contents'";
			$check = $sm->get( 'Application\Model\MoneyCbTable' ) -> fetchAll( false, false, $where );
			if($check -> count() == 0){
				$sm->get( 'Application\Model\MoneyCbTable' )->save( $arrSave,false, false, true );
			}
			else{
				$arrUpdate['id_cb'] = $id_cb;
				$arrUpdate['id_contents'] = $id_contents;
				$sm->get( 'Application\Model\MoneyCbTable' )->update($arrSave, $arrUpdate);
			}

			//категория 6
			$arrSave = array();
			$arrSave['id_category'] = 6;
			$arrSave['id_brand'] = $id_brand;
			$where = "category_brand.id_category = '{$arrSave['id_category']}' and category_brand.id_brand = '$id_brand'";
			$check = $sm->get( 'Application\Model\CategoryBrandTable' ) -> fetchAll(false, false, $where);
			if($check -> count() == 0){
				$id_cb = $sm->get( 'Application\Model\CategoryBrandTable' )->save( $arrSave,false, false, true );
			}
			else{
				$id_cb = $check -> current();
				$id_cb = $id_cb -> id_cb;
			}
			$arrSave = array();
			$arrSave['id_cb'] = $id_cb;
			$arrSave['id_contents'] = $id_contents;
			$arrSave['money_cb'] = (empty($arr[6]))?0:$arr[6];
			$where = "money_cb.id_cb = '$id_cb' and money_cb.id_contents = '$id_contents'";
			$check = $sm->get( 'Application\Model\MoneyCbTable' ) -> fetchAll( false, false, $where );
			if($check -> count() == 0){
				$sm->get( 'Application\Model\MoneyCbTable' )->save( $arrSave,false, false, true );
			}
			else{
				$arrUpdate['id_cb'] = $id_cb;
				$arrUpdate['id_contents'] = $id_contents;
				$sm->get( 'Application\Model\MoneyCbTable' )->update($arrSave, $arrUpdate);
			}

			//категория 7
			$arrSave = array();
			$arrSave['id_category'] = 8;
			$arrSave['id_brand'] = $id_brand;
			$where = "category_brand.id_category = '{$arrSave['id_category']}' and category_brand.id_brand = '$id_brand'";
			$check = $sm->get( 'Application\Model\CategoryBrandTable' ) -> fetchAll(false, false, $where);
			if($check -> count() == 0){
				$id_cb = $sm->get( 'Application\Model\CategoryBrandTable' )->save( $arrSave,false, false, true );
			}
			else{
				$id_cb = $check -> current();
				$id_cb = $id_cb -> id_cb;
			}
			$arrSave = array();
			$arrSave['id_cb'] = $id_cb;
			$arrSave['id_contents'] = $id_contents;
			$arrSave['money_cb'] = (empty($arr[7]))?0:$arr[7];
			$where = "money_cb.id_cb = '$id_cb' and money_cb.id_contents = '$id_contents'";
			$check = $sm->get( 'Application\Model\MoneyCbTable' ) -> fetchAll( false, false, $where );
			if($check -> count() == 0){
				$sm->get( 'Application\Model\MoneyCbTable' )->save( $arrSave,false, false, true );
			}
			else{
				$arrUpdate['id_cb'] = $id_cb;
				$arrUpdate['id_contents'] = $id_contents;
				$sm->get( 'Application\Model\MoneyCbTable' )->update($arrSave, $arrUpdate);
			}

			//категория 8
			$arrSave = array();
			$arrSave['id_category'] = 9;
			$arrSave['id_brand'] = $id_brand;
			$where = "category_brand.id_category = '{$arrSave['id_category']}' and category_brand.id_brand = '$id_brand'";
			$check = $sm->get( 'Application\Model\CategoryBrandTable' ) -> fetchAll(false, false, $where);
			if($check -> count() == 0){
				$id_cb = $sm->get( 'Application\Model\CategoryBrandTable' )->save( $arrSave,false, false, true );
			}
			else{
				$id_cb = $check -> current();
				$id_cb = $id_cb -> id_cb;
			}
			$arrSave = array();
			$arrSave['id_cb'] = $id_cb;
			$arrSave['id_contents'] = $id_contents;
			$arrSave['money_cb'] = (empty($arr[8]))?0:$arr[8];
			$where = "money_cb.id_cb = '$id_cb' and money_cb.id_contents = '$id_contents'";
			$check = $sm->get( 'Application\Model\MoneyCbTable' ) -> fetchAll( false, false, $where );
			if($check -> count() == 0){
				$sm->get( 'Application\Model\MoneyCbTable' )->save( $arrSave,false, false, true );
			}
			else{
				$arrUpdate['id_cb'] = $id_cb;
				$arrUpdate['id_contents'] = $id_contents;
				$sm->get( 'Application\Model\MoneyCbTable' )->update($arrSave, $arrUpdate);
			}

			//категория 9
			$arrSave = array();
			$arrSave['id_category'] = 10;
			$arrSave['id_brand'] = $id_brand;
			$where = "category_brand.id_category = '{$arrSave['id_category']}' and category_brand.id_brand = '$id_brand'";
			$check = $sm->get( 'Application\Model\CategoryBrandTable' ) -> fetchAll(false, false, $where);
			if($check -> count() == 0){
				$id_cb = $sm->get( 'Application\Model\CategoryBrandTable' )->save( $arrSave,false, false, true );
			}
			else{
				$id_cb = $check -> current();
				$id_cb = $id_cb -> id_cb;
			}
			$arrSave = array();
			$arrSave['id_cb'] = $id_cb;
			$arrSave['id_contents'] = $id_contents;
			$arrSave['money_cb'] = (empty($arr[9]))?0:$arr[9];
			$where = "money_cb.id_cb = '$id_cb' and money_cb.id_contents = '$id_contents'";
			$check = $sm->get( 'Application\Model\MoneyCbTable' ) -> fetchAll( false, false, $where );
			if($check -> count() == 0){
				$sm->get( 'Application\Model\MoneyCbTable' )->save( $arrSave,false, false, true );
			}
			else{
				$arrUpdate['id_cb'] = $id_cb;
				$arrUpdate['id_contents'] = $id_contents;
				$sm->get( 'Application\Model\MoneyCbTable' )->update($arrSave, $arrUpdate);
			}

			//категория Кожа + кожзам
			$arrSave = array();
			$arrSave['id_category'] = 11;
			$arrSave['id_brand'] = $id_brand;
			$where = "category_brand.id_category = '{$arrSave['id_category']}' and category_brand.id_brand = '$id_brand'";
			$check = $sm->get( 'Application\Model\CategoryBrandTable' ) -> fetchAll(false, false, $where);
			if($check -> count() == 0){
				$id_cb = $sm->get( 'Application\Model\CategoryBrandTable' )->save( $arrSave,false, false, true );
			}
			else{
				$id_cb = $check -> current();
				$id_cb = $id_cb -> id_cb;
			}
			$arrSave = array();
			$arrSave['id_cb'] = $id_cb;
			$arrSave['id_contents'] = $id_contents;
			$arrSave['money_cb'] = (empty($arr[10]))?0:$arr[10];
			$where = "money_cb.id_cb = '$id_cb' and money_cb.id_contents = '$id_contents'";
			$check = $sm->get( 'Application\Model\MoneyCbTable' ) -> fetchAll( false, false, $where );
			if($check -> count() == 0){
				$sm->get( 'Application\Model\MoneyCbTable' )->save( $arrSave,false, false, true );
			}
			else{
				$arrUpdate['id_cb'] = $id_cb;
				$arrUpdate['id_contents'] = $id_contents;
				$sm->get( 'Application\Model\MoneyCbTable' )->update($arrSave, $arrUpdate);
			}

			//категория Натуральная кожа
			$arrSave = array();
			$arrSave['id_category'] = 7;
			$arrSave['id_brand'] = $id_brand;
			$where = "category_brand.id_category = '{$arrSave['id_category']}' and category_brand.id_brand = '$id_brand'";
			$check = $sm->get( 'Application\Model\CategoryBrandTable' ) -> fetchAll(false, false, $where);
			if($check -> count() == 0){
				$id_cb = $sm->get( 'Application\Model\CategoryBrandTable' )->save( $arrSave,false, false, true );
			}
			else{
				$id_cb = $check -> current();
				$id_cb = $id_cb -> id_cb;
			}
			$arrSave = array();
			$arrSave['id_cb'] = $id_cb;
			$arrSave['id_contents'] = $id_contents;
			$arrSave['money_cb'] = (empty($arr[11]))?0:$arr[11];
			$where = "money_cb.id_cb = '$id_cb' and money_cb.id_contents = '$id_contents'";
			$check = $sm->get( 'Application\Model\MoneyCbTable' ) -> fetchAll( false, false, $where );
			if($check -> count() == 0){
				$sm->get( 'Application\Model\MoneyCbTable' )->save( $arrSave,false, false, true );
			}
			else{
				$arrUpdate['id_cb'] = $id_cb;
				$arrUpdate['id_contents'] = $id_contents;
				$sm->get( 'Application\Model\MoneyCbTable' )->update($arrSave, $arrUpdate);
			}
		}
		die();
	}

	public function Akvilon(){
		die();
		$id_brand = 13; //аквилон
		$id_type = 20;
		$id_type_field = 108;
		$sm = $this->getServiceLocator();
		$this -> folders = 'Akvilon';

		$dir = $_SERVER['DOCUMENT_ROOT'] . "/parser/".$this -> folders;;
		$file = $dir .'/'.'tmp.html';
		$contents = file_get_contents($file);
		//$contents = preg_replace('/src="(.*)"/', '' ,$contents);
		preg_match_all('/<tr.*>(.*)<\/tr>/isuU',  $contents, $m  );

//		print_r($m);
//		print_r($contents);
//		die();
		foreach($m[1] as $k => $v){
			preg_match_all('/<td.*>(.*)<\/td>
			/xisU',  $v, $td  );

			$count = count($td[1]);
			$arr = array();
			foreach($td[1] as $b){
				$arr[] = $this -> clearStr($b);
			}

			$arrSave = array();
			$arrHelp[0] = $arr[0];
			$arrHelp[1] = $arr[1];
			$arrHelp[2] = $arr[2];
			$arr = array();
			$arr = $arrHelp;
			if(!is_numeric($arrHelp[1])) continue;
			if(!is_numeric($arrHelp[2])) continue;

			$arrSave['id_menu'] = 0;
			$arrSave['name_contents'] = $arr[0];
			$arrSave['money_contents'] = (!empty($arrHelp[1]))?$arrHelp[1]:0;
			$arrSave['id_brand'] = $id_brand;
			$arrSave['name_parser_contents'] = $arrHelp[0];
			$arrSave['alias_contents'] = $this -> translitIt($arrHelp[0]);
			$arrSave['alias_contents'] = preg_replace('/(_{2,})/isU', '_', $arrSave['alias_contents']);
			$arrSave['datetime_create'] = date("Y-m-d H:i:s");
			$arrSave['id_type'] = $id_type;
			$arrSave['vis_contents'] = 0;
			$arrSave['id_moderator'] = 5;
			$where = "contents.name_parser_contents = '".$arr[0]."'";
			$check = $sm->get( 'Application\Model\ContentsTable' ) -> fetchAll( false, false, $where );

			if($check -> count() == 0){
				$id_contents = $sm->get( 'Application\Model\ContentsTable' )->save( $arrSave,false, false, true );
			}
			else{
				$id_contents = $check -> current();
				$id_contents = $id_contents -> id_contents;
				$arrT['money_contents'] = $arrSave['money_contents'];
				$id_contents = $sm->get( 'Application\Model\ContentsTable' )->save( $arrT, $id_contents, 'id_contents',true);
			}
			if(empty($id_contents)) continue;
			$where = "values.id_type_field = $id_type_field and values.id_contents = $id_contents";
			$check = $sm->get( 'Application\Model\ValuesTable' ) -> fetchAll( false, false, $where );
			if($check -> count() == 0){
				$arrSave =array();
				$arrSave['id_type_field'] = $id_type_field;
				$arrSave['id_contents'] = $id_contents;
				$arrSave['varchar_value'] = $arrHelp[2];
				$sm->get( 'Application\Model\ValuesTable' )->save( $arrSave,false, false, true );
			}
			else{
				$arrSave =array();
				$arrSave['id_type_field'] = $id_type_field;
				$arrSave['id_contents'] = $id_contents;
				$arrSave['varchar_value'] = $arrHelp[2];
				$where = "values.id_type_field = $id_type_field and values.id_contents = $id_contents";
				$sm->get( 'Application\Model\ValuesTable' ) -> updateArr( $arrSave,false, false, $where );
			}
		}
		die();
	}

	public function Borovichi_myagkaya_mebeli(){
		die();
		$id_brand = 34;
		$id_type = 19;
		$sm = $this->getServiceLocator();
		$id = $this -> params() -> fromRoute('id');
		$arr['1'] = 'Borovichi_myagkaya_mebeli';
		foreach($arr as $k => $v){
			if($k == $id){
				$this -> folders = $v;
			}
		}
		$dir = $_SERVER['DOCUMENT_ROOT'] . "/parser/".$this -> folders;;
		$file = $dir .'/'.'tmp.html';
		$contents = file_get_contents($file);
		preg_match_all('/<tr.*>(.*)<\/tr>
			/xisU',  $contents, $m  );

		foreach($m[1] as $k => $v){
			if($k == 0 or $k == 1 or $k == 2) continue;
			preg_match_all('/<td.*>(.*)<\/td>
			/xisU',  $v, $td  );
			$count = count($td[1]);
			$arr = array();
			foreach($td[1] as $b){
				$arr[] = $this -> clearStr($b);
			}
			$arrSave = array();
			if($count == 4){
				$arrHelp[0] = $arr[0];
				$arrHelp[1] = "";
				$arrHelp[2] = $arr[1];
				$arrHelp[3] = $arr[2];
				$arrHelp[4] = $arr[3];
				$arr = array();
				$arr = $arrHelp;
			}

			if($count != 4 and $count !=5) continue;
			$arrSave['id_menu'] = 0;
			$arrSave['name_contents'] = $arr[0];
			$arrSave['money_contents'] = (empty($arr[2]))?((empty($arr[3]))?((empty($arr[4]))?'0':$arr[4]):$arr[3]):($arr[2]);
			$arrSave['id_brand'] = $id_brand;
			$arrSave['name_parser_contents'] = $arr[0];
			$arrSave['alias_contents'] = $this -> translitIt($arr[0]);
			$arrSave['alias_contents'] = preg_replace('/(_{2,})/isU', '_', $arrSave['alias_contents']);
			$arrSave['datetime_create'] = date("Y-m-d H:i:s");
			$arrSave['id_type'] = $id_type;
			$arrSave['vis_contents'] = 0;
			$where = "contents.name_parser_contents = '".$arr[0]."'";
			$check = $sm->get( 'Application\Model\ContentsTable' ) -> fetchAll( false, false, $where );

			if($check -> count() == 0){
				$id_contents = $sm->get( 'Application\Model\ContentsTable' )->save( $arrSave,false, false, true );
			}
			else{
				$id_contents = $check -> current();
				$id_contents = $id_contents -> id_contents;
				$arrT['money_contents'] = $arrSave['money_contents'];
				$id_contents = $sm->get( 'Application\Model\ContentsTable' )->save( $arrT, $id_contents, 'id_contents',true);
			}
			if(empty($id_contents)) continue;
			//категория 1
			$arrSave = array();
			$arrSave['id_category'] = 1;
			$arrSave['id_brand'] = $id_brand;
			$where = "category_brand.id_category = '1' and category_brand.id_brand = '$id_brand'";
			$check = $sm->get( 'Application\Model\CategoryBrandTable' ) -> fetchAll(false, false, $where);
			if($check -> count() == 0){
				$id_cb = $sm->get( 'Application\Model\CategoryBrandTable' )->save( $arrSave,false, false, true );
			}
			else{
				$id_cb = $check -> current();
				$id_cb = $id_cb -> id_cb;
			}
			$arrSave = array();
			$arrSave['id_cb'] = $id_cb;
			$arrSave['id_contents'] = $id_contents;
			$arrSave['money_cb'] = (empty($arr[2]))?0:$arr[2];
			$where = "money_cb.id_cb = '$id_cb' and money_cb.id_contents = '$id_contents'";
			$check = $sm->get( 'Application\Model\MoneyCbTable' ) -> fetchAll( false, false, $where );
			if($check -> count() == 0){
				$sm->get( 'Application\Model\MoneyCbTable' )->save( $arrSave,false, false, true );
			}
			else{
				$arrUpdate['id_cb'] = $id_cb;
				$arrUpdate['id_contents'] = $id_contents;
				$sm->get( 'Application\Model\MoneyCbTable' )->update($arrSave, $arrUpdate);
			}

			$arrSave = array();
			$arrSave['id_category'] = 2;
			$arrSave['id_brand'] = $id_brand;
			$where = "category_brand.id_category = '2' and category_brand.id_brand = '$id_brand'";
			$check = $sm->get( 'Application\Model\CategoryBrandTable' ) -> fetchAll(false, false, $where);
			if($check -> count() == 0){
				$id_cb = $sm->get( 'Application\Model\CategoryBrandTable' )->save( $arrSave,false, false, true );
			}
			else{
				$id_cb = $check -> current();
				$id_cb = $id_cb -> id_cb;
			}
			$arrSave = array();
			$arrSave['id_cb'] = $id_cb;
			$arrSave['id_contents'] = $id_contents;
			$arrSave['money_cb'] = (empty($arr[3]))?0:$arr[3];
			$where = "money_cb.id_cb = '$id_cb' and money_cb.id_contents = '$id_contents'";
			$check = $sm->get( 'Application\Model\MoneyCbTable' ) -> fetchAll( false, false, $where );
			if($check -> count() == 0){
				$sm->get( 'Application\Model\MoneyCbTable' )->save( $arrSave,false, false, true );
			}
			else{
				$arrUpdate['id_cb'] = $id_cb;
				$arrUpdate['id_contents'] = $id_contents;
				$sm->get( 'Application\Model\MoneyCbTable' )->update($arrSave, $arrUpdate);
			}
			$arrSave = array();
			$arrSave['id_category'] = 7;
			$arrSave['id_brand'] = $id_brand;
			$where = "category_brand.id_category = '7' and category_brand.id_brand = '$id_brand'";
			$check = $sm->get( 'Application\Model\CategoryBrandTable' ) -> fetchAll(false, false, $where);
			if($check -> count() == 0){
				$id_cb = $sm->get( 'Application\Model\CategoryBrandTable' )->save( $arrSave,false, false, true );
			}
			else{
				$id_cb = $check -> current();
				$id_cb = $id_cb -> id_cb;
			}
			$arrSave = array();
			$arrSave['id_cb'] = $id_cb;
			$arrSave['id_contents'] = $id_contents;
			$arrSave['money_cb'] = (empty($arr[4]))?0:$arr[4];
			$where = "money_cb.id_cb = '$id_cb' and money_cb.id_contents = '$id_contents'";
			$check = $sm->get( 'Application\Model\MoneyCbTable' ) -> fetchAll( false, false, $where );
			if($check -> count() == 0){
				$sm->get( 'Application\Model\MoneyCbTable' )->save( $arrSave,false, false, true );
			}
			else{
				$arrUpdate['id_cb'] = $id_cb;
				$arrUpdate['id_contents'] = $id_contents;
				$sm->get( 'Application\Model\MoneyCbTable' )->update($arrSave, $arrUpdate);
			}
		}
	}

	public function clearStr($str)
	{
		$str = strip_tags($str);
		$str = str_replace(array("\xC2\xA0", "\r\n", "\r", "\n", "&nbsp;", "&#160;", 'г.', ';', ':', '&laquo', '&quot', '&raquo', '&#171', '&#187', '№', 'года\.'),
			" ",
			rtrim($str, "\xC2"));
		$str = preg_replace('#\s{2,}#isu', ' ', $str);
		$str = preg_replace('#(www\..*?\.ru)#iu', '', $str);
		$str = trim(str_replace(array('&quot;', '&laquo;', '&raquo;'),
			'"',
			$str));
		$str = trim(str_replace(array('&mdash;', '»', '«', '№', ':'), array('-', '', '', '', ''), $str));
		if(empty($str)){
			$str = NULL;
		}
		return $str;
	}

	public function translitIt($str)
	{
		$tr = array(
			"А"=>"a","Б"=>"b","В"=>"v","Г"=>"g",
			"Д"=>"d","Е"=>"e","Ж"=>"j","З"=>"z","И"=>"i",
			"Й"=>"y","К"=>"k","Л"=>"l","М"=>"m","Н"=>"n",
			"О"=>"o","П"=>"p","Р"=>"r","С"=>"s","Т"=>"t",
			"У"=>"u","Ф"=>"f","Х"=>"h","Ц"=>"ts","Ч"=>"ch",
			"Ш"=>"sh","Щ"=>"sch","Ъ"=>"","Ы"=>"yi","Ь"=>"",
			"Э"=>"e","Ю"=>"yu","Я"=>"ya","а"=>"a","б"=>"b",
			"в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
			"з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
			"м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
			"с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
			"ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
			"ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",
			" "=> "_", "."=> "", ","=> "_", "-"=> "_", "/"=> "_","\"" => "_","*" => "_",")" => "_","(" => "_","+" => "_"
		);
		$r = strtr($str,$tr);
		$r = preg_replace('/(_{2,})/isU', '_', $r);
		$r = trim($r,"_");
		return $r;
	}


}

?>