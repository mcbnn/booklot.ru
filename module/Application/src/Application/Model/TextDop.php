<?php
namespace Application\Model;

class TextDop
{

    public function exchangeArray($data)
    {
		foreach($data as $k => $v){
			$this->arr[$k] = $v;
			$this->$k = $v;
		}
    }
	public function __set($k, $v){
		$this->arr[$k] = $v;
		$this->$k = $v;
	}

}