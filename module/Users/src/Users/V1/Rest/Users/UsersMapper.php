<?php
namespace Users\V1\Rest\Users;

use Zend\Db\Sql\Select;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;

class UsersMapper
{
    protected $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }
 
    public function fetchAll()
    {
        $select = new Select('users');
        $paginatorAdapter = new DbSelect($select->order('id'), $this->adapter);
        $collection = new UsersCollection($paginatorAdapter);
        return $collection;
    }
 
    public function fetchOne($id)
    {
        $sql = 'SELECT * FROM users WHERE id = ?';
        $resultset = $this->adapter->query($sql, array($id));
        $data = $resultset->toArray();
        if (!$data) {
            return false;
        }
 
        $entity = new UsersEntity();
        $entity->exchangeArray($data[0]);
        return $entity;
    }

    public function create($data)
    {
        $sql = 'INSERT INTO users (bejnev, email) values(?, ?)';
        $resultset = $this->adapter->query($sql, array($data->bejnev, $data->email));

        $data->id = $resultset->getGeneratedValue();
 
        $entity = new UsersEntity();
        $entity->exchangeArray((array)$data);
        return $entity;
    }  

    public function update($data)
    {
        $sql = 'UPDATE users SET bejnev = ?, email = ? WHERE id = ?';
        $this->adapter->query($sql, array($data->bejnev, $data->email, $data->id));
 
        $entity = new UsersEntity();
        $entity->exchangeArray((array)$data);
        return $entity;
    }   

    public function delete($id)
    {
        $sql = 'DELETE FROM users WHERE id = ?';
        $this->adapter->query($sql, array($id));
        return true;
    }    

}