<?php
namespace Application\Model;

class History
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