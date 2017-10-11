<?php

namespace Application\Service;
use \Zend\Paginator\Adapter\DbSelect;
use \Zend\Db\Sql\Expression;


use \Zend\Mvc\Controller\AbstractActionController;
use \Zend\View\Model\ViewModel;

class MyDbSelect extends \Zend\Paginator\Adapter\DbSelect
	
	
{
    public function count()
    {	
        $select = new \Zend\Db\Sql\Select();
		
		 
	 	 
        $select->from('m_zhanr')->where(array('id'=>'502'))->columns(array('c'=>new Expression('sum(count_book)')));

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        $row       = $result->current();
        $this->rowCount = $row['c'];

        return $this->rowCount;
    }
}
 

?>