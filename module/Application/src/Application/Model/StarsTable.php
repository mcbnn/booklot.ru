<?php

namespace Application\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;


class StarsTable
{
	protected $tableGateway;
	protected $sql;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
		$this->sql = $this->tableGateway->getSql()->select();
	}

	public function fetchAll($paginator = true, $order = false, $where = false, $limit = false, $groupBy = false)
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
		// print_r($sql->getSqlString());die();
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
            if ($this->getId($where)) {
                $this->update($data, $where);
                if ($return) {
                    return var_dump(get_class_methods($this));die();
                }
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function delete($name, $val)
    {
        $this->tableGateway->delete(array($name => $val));
    }

}