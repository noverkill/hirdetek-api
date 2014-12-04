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
 
    public function fetchOne($id, $email)
    {
        $sql = 'SELECT * FROM users WHERE email = ? LIMIT 1';
        $resultset = $this->adapter->query($sql, array($email));
        
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
        $sql = 'INSERT INTO users (nev, email, jelszo, felvetel, aktivkod, aktiv) values(?, ?, ?, NOW(), ?, 0)';
        $resultset = $this->adapter->query($sql, array($data->nev, $data->email, $data->jelszo, 'abcd123456'));

        $data->id = $resultset->getGeneratedValue();
        
        /*
        $entity = new UsersEntity();
        $entity->exchangeArray((array)$data);
        return $entity;
        */
    }  

    public function update($data, $email)
    {
        $sql = 'UPDATE users SET nev = ?, weblap = ?, telefon = ?, varos = ?, regio = ?, altkategoria = ? WHERE id = ? AND email = ?';
        
        $this->adapter->query($sql, array($data->nev, $data->weblap, $data->telefon, $data->varos, $data->regio, $data->altkategoria, $data->id, $email));

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