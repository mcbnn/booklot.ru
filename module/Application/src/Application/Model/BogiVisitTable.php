<?php

namespace Application\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

use Zend\Db\Adapter\AdapterAwareInterface;

class BogiVisitTable
{
    protected $tableGateway;

    protected $column = "id";
    protected $table = "bogi_visit";

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
		$this->sql = $this->tableGateway->getSql()->select();
	}

	public function fetchAll($paginator = true, $order = false, $where = false, $limit = false, $groupBy = false)
	{
		if (!empty($where)) {
			$this->sql->where($where);
		}
		if (!empty($order)) {
			$this->sql->order($order);
		}
		if (!empty($limit)) {
			$this->sql->limit($limit);
		}
		if (!empty($groupBy)) {
			$this->sql->group($groupBy);
		}
		//	    print_r($where);
		//	    print_r($this-> sql -> getSqlString());die();
		if ($paginator) {
			$paginatorAdapter = new \Zend\Paginator\Adapter\DbSelect($this->sql, $this->tableGateway->adapter);
			$resultSet = new \Zend\Paginator\Paginator($paginatorAdapter);
		} else {
			$resultSet = $this->tableGateway->selectWith($this->sql);
			$resultSet->buffer();
		}
		$this->sql = $this->tableGateway->getSql()->select();
		return $resultSet;
	}

    public function joinParentMenu()
    {
        $this->sql->join(array('m1' => 'menu'), 'm1.id_menu=menu.id_menu_main', array('labelParentMenu' => 'label_menu'), 'left');
        return $this;
    }


    public function getId($var, $id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array($var => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function update($data, $idName = false, $id = false)
    {
        if (empty($idName) and empty($id)) {
            throw new \Exception("Нет имениId или значенияId");
        }
        $this->tableGateway->update($data, array($idName => $id));
    }

	public function save($data, $where = false,  $return = false)
	{
		$dataArr = array();
		if(is_object($data)){
			foreach($data -> data as $k => $v){
				if(!empty($v)){
					$dataArr[$k] = $v;
				}
			}
		}
		else{
			$dataArr = $data;
		}
		$data = $dataArr;
		if (empty($where)) {

			$this->tableGateway->insert($data);
			if ($return) {
                $sql = "Select ".$this->column." from ".$this->table." order by ".$this->column." desc limit 1";
                $content = $this->tableGateway->getAdapter()->query($sql)->execute()->current();
                return  $content[$this->column];
			}
		} else {
			$this->updateNew($data, $where);
			if ($return) {
				return $where['id'];

			}
		}
	}

	public function updateNew($data, $where)
	{

		$this->tableGateway->update($data, $where);

	}

	public function delete($name, $val)
    {
        $this->tableGateway->delete(array($name => $val));
    }

	public function deleteArr($arr)
	{
		$this->tableGateway->delete($arr);
	}
}