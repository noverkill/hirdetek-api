<?php
namespace hirdetek\V1\Rest\Hirdetes;

use Zend\Db\Sql\Select;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;

class HirdetesMapper
{
    protected $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }
 
    public function fetchAll()
    {
        $select = new Select('hirdetes');
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        $collection = new HirdetesCollection($paginatorAdapter);
        return $collection;
    }
 
    public function fetchOne($id)
    {
        $sql = 'SELECT * FROM hirdetes WHERE id = ?';
        $resultset = $this->adapter->query($sql, array($id));
        $data = $resultset->toArray();
        if (!$data) {
            return false;
        }
 
        $entity = new HirdetesEntity();
        $entity->exchangeArray($data[0]);
        return $entity;
    }

    public function create($data)
    {
        //print_r(array_values((array) $data));
        //exit;

        $sql = 'INSERT INTO hirdetes (szoveg, kep) values(?, ?)';
        $resultset = $this->adapter->query($sql, array($data->szoveg, $data->kep));

        $data->id = $resultset->getGeneratedValue();
 
        $entity = new HirdetesEntity();
        $entity->exchangeArray((array)$data);
        return $entity;
    }   
    

}