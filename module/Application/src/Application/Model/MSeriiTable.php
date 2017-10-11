<?php

namespace Application\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;


class MSeriiTable
{
	protected $tableGateway;
	protected $sql;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
		$this->sql = $this->tableGateway->getSql()->select();
	}


	public function fetchAll($paginator = true, $order = false, $where = false, $limit = false, $groupBy = false, $having = false, $columns = false)
	{
//        $sql->join('user_task', 'user_task.id_user_task=delay_mail.id_user_task', array('status'));
//        $sql->join('user', 'user.id_user=user_task.id_user', array('name'));
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
		if (!empty($having)) {
			$this->sql->having($having);
		}
		if (!empty($columns)) {
			$this->sql->columns($columns);
		}
		//print_r($this->sql->getSqlString());die();
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

	public function joinSerii(){
		$this->sql->join('serii','serii.id_menu = m_serii.id', array('id_menu'), 'inner');
		return $this;
	}

	public function joinBook(){
		$this->sql->join('book','book.id = serii.id_main', array('book_alias' => 'alias', 'book_name' => 'name', 'foto', 'book_id' => 'id'), 'inner');
		return $this;
	}

    public function getId($where)
    {

        $rowset = $this->tableGateway->select($where);
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row");
        }
        return $row;
    }

    public function update($data, $where)
    {
        if (empty($where)) {
            throw new \Exception("Нет имениId или значенияId");
        }
        $this->tableGateway->update($data, $where);
    }

    public function save($data, $where = false, $return = false)
    {


        if (empty($where)) {
            $this->tableGateway->insert($data);
            if ($return) {
                return $this->tableGateway->getLastInsertValue();
            }
        } else {
                $this->update($data, $where);
                if ($return) {
                    return var_dump(get_class_methods($this));die();

            }
        }
    }

    public function delete($name, $val)
    {
        $this->tableGateway->delete(array($name => $val));
    }

}
