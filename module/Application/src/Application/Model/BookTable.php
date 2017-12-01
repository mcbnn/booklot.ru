<?php

namespace Application\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Expression;
use Application\Service\MyDbSelect;
use Zend\Cache\Storage\StorageInterface;

class BookTable {

    protected $tableGateway;
    protected $sql;
    protected $cache;

    protected $column = "id";
    protected $table = "book";

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
        $this->sql = $this->tableGateway->getSql()->select();
    }

    public function setCache(StorageInterface $cache)
    {
        $this->cache = $cache;
    }

    public function fetchAll($paginator = true, $order = false, $where = false, $limit = false, $groupBy = false, $having = false, $columns = false) {

        if (!empty($where)) {
            $this->sql->where($where);
        }
        if (!empty($order)) {

            $this->sql->order(new Expression($order));
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
        };

        $md5 = md5($this->sql->getSqlString());
        if( ($resultSet = $this->cache->getItem($md5)) == FALSE) {
            if ($paginator) {
                $paginatorAdapter = new \Zend\Paginator\Adapter\DbSelect($this->sql, $this->tableGateway->adapter);
                $resultSet = new \Zend\Paginator\Paginator($paginatorAdapter);

            }
            else {
                $resultSet = $this->tableGateway->selectWith($this->sql);
                $resultSet->buffer();
            }

            $this->cache->setItem($md5,  $resultSet );
        }

        $this->sql = $this->tableGateway->getSql()->select();

        return $resultSet;
    }

    public function offset($num) {
        $this->sql->offset($num);

        return $this;
    }

    public function limit($num) {
        $this->sql->limit($num);

        return $this;
    }

    public function joinColumn($arr) {

        $this->sql->columns($arr);

        return $this;

    }

    public function columnCountTable() {
        $this->sql->columns([
                'countText' => new Expression("(Select count(id) from text where text.id_main=book.id
					)"),
                "*"
            ]

        );

        return $this;
    }

    public function columnCountTwoTable() {
        $this->sql->columns([
                'c' => new Expression("count(*)"),
                "*"
            ]

        );

        return $this;
    }

    public function joinSerii() {
        $this->sql->join('serii', 'serii.id_main = book.id', [ ], 'inner');

        return $this;
    }

    public function joinMSerii() {
        $this->sql->join('m_serii', 'm_serii.id = serii.id_menu', [], 'inner');

        return $this;
    }

    public function joinSeriiLeft() {
        $this->sql->join('serii', 'serii.id_main = book.id', [], 'left');

        return $this;
    }

    public function joinMSeriiLeft() {
        $this->sql->join('m_serii', 'm_serii.id = serii.id_menu', [], 'left');

        return $this;
    }

    public function joinTranslit() {
        $this->sql->join('translit', 'translit.id_main = book.id', [], 'inner');

        return $this;
    }

    public function joinMTranslit() {
        $this->sql->join('m_translit', 'm_translit.id = translit.id_menu', [], 'inner');

        return $this;
    }

    public function joinTranslitLeft() {
        $this->sql->join('translit', 'translit.id_main = book.id', [ ], 'left');

        return $this;
    }

    public function joinMTranslitLeft() {
        $this->sql->join('m_translit', 'm_translit.id = translit.id_menu', [], 'left');

        return $this;
    }

    public function joinAvtor() {
        $this->sql->join('avtor', 'avtor.id_main = book.id', [  ], 'inner');

        return $this;
    }

    public function joinMAvtor() {
        $this->sql->join('m_avtor', 'm_avtor.id = avtor.id_menu', [], 'inner');

        return $this;
    }

    public function joinAvtorLeft() {
        $this->sql->join('avtor', 'avtor.id_main = book.id', [ ], 'left');

        return $this;
    }

    public function joinMAvtorLeft() {
        $this->sql->join('m_avtor', 'm_avtor.id = avtor.id_menu', [], 'left');

        return $this;
    }

    public function joinZhanr() {

        $this->sql->join('zhanr', 'zhanr.id_main = book.id', [], 'inner');

        return $this;
    }

    public function joinMZhanr() {

        $this->sql->join([ 'mz0' => 'm_zhanr' ], 'mz0.id = zhanr.id_menu', [], 'inner');

        return $this;
    }

    public function joinMZhanrParent() {
        $this->sql->join([ 'mz1' => 'm_zhanr' ], 'mz0.id_main = mz1.id', [], 'Left');

        return $this;
    }

    public function getId($where) {

        $rowset = $this->tableGateway->select($where);
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row");
        }

        return $row;
    }

    public function update($data, $where) {
        if (empty($where)) {
            throw new \Exception("Нет имениId или значенияId");
        }
        $this->tableGateway->update($data, $where);
    }

    public function save($data, $where, $return = false) {

        if (empty($where)) {
            $this->tableGateway->insert($data);
            if ($return) {
                $sql = "Select ".$this->column." from ".$this->table." order by ".$this->column." desc limit 1";
                $content = $this->tableGateway->getAdapter()->query($sql)->execute()->current();
                return  $content[$this->column];
            }
        }
        else {

            $this->update($data, $where);
            if ($return) {
                return var_dump(get_class_methods($this));
                die();
            }
        }

    }

    public function delete($name, $val) {
        $this->tableGateway->delete([ $name => $val ]);
    }

}
