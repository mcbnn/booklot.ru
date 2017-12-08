<?php
namespace Application\Model;

class BogiVisit
{
	public function exchangeArray($data)
	{
		foreach($data as $k => $v){
			$this->data[$k] = $v;
			$this->$k = $v;
		}
	}
	public function __set($k, $v){
		$this->data[$k] = $v;
		$this->$k = $v;
	}
}