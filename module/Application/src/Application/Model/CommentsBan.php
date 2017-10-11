<?php
namespace Application\Model;

class CommentsBan
{

    public function exchangeArray($data)
    {
		foreach($data as $k => $v){
			$this->arr[$k] = $v;
		}
    }
	public function __set($k, $v){
		$this->arr[$k] = $v;
	}

}