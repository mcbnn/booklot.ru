<?php

namespace Application\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;
use Zend\Cache\Storage\StorageInterface;

class MZhanrTable
{
	protected $tableGateway;
	protected $sql;

    protected $column = "id";
    protected $table = "m_zhanr";

    /** @var  $cache \Zend\Cache\Storage\Adapter\Redis */
    protected $cache;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
		$this->sql = $this->tableGateway->getSql()->select();
	}

    public function setCache(StorageInterface $cache)
    {

        $this->cache = $cache;
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


        $md5 = md5($this->sql->getSqlString($this->tableGateway->getAdapter()->getPlatform()));
        //$this->cache->flush();
        if( ($resultSet = $this->cache->getItem($md5)) == FALSE) {

            if ($paginator) {
                $paginatorAdapter = new \Zend\Paginator\Adapter\DbSelect($this->sql, $this->tableGateway->adapter);
                $resultSet = new \Zend\Paginator\Paginator($paginatorAdapter);
            }
            else {
                $resultSet = $this->tableGateway->selectWith($this->sql);
                $resultSet = $resultSet->toArray();
                $this->cache->setItem($md5, $resultSet);
            }
        }

        $this->sql = $this->tableGateway->getSql()->select();
        if(is_array($resultSet))$resultSet = $this->convToObj($resultSet);
        return $resultSet;
	}

    public function setTtl($Ttl){

        $this->cache->getOptions()->setTtl($Ttl);
        return $this;
    }

    public function convToObj($arr){
        foreach ($arr as &$v){
            $v = (object)$v;
        }
        return $arr;
    }
	
	public function columnSummTable(){
		$this->sql->columns(array('summBook'=>new Expression("sum(count_book)")));
		return $this;
	}

	public function joinZhanr(){
		$this->sql->join('zhanr','zhanr.id_menu = m_zhanr.id', array('id_menu'), 'inner');
		return $this;
	}

	public function joinBook(){
		$this->sql->join('book','book.id = zhanr.id_main', array('book_alias' => 'alias', 'book_name' => 'name', 'foto'), 'inner');
		return $this;
	}

	
    public function getId($where)
    {

        $rowset = $this->tableGateway->select($where);
	    var_dump($where);diE();
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
                $sql = "Select ".$this->column." from ".$this->table." order by ".$this->column." desc limit 1";
                $content = $this->tableGateway->getAdapter()->query($sql)->execute()->current();
                return  $content[$this->column];
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
