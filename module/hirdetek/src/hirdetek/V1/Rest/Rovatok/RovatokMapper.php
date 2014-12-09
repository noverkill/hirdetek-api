<?php
namespace hirdetek\V1\Rest\Rovatok;

use Zend\Db\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Expression;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;

class RovatokMapper
{
    protected $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function fetchAll($params)
    {
        $select = (new Select())->from('rovat')->order('order');

        // forovatok with sub-categorie's count
        /* 
        $select = (new Select())
                    ->columns( array('id', 'nev', 'slug', 'order', 'parent', 'p_count' => new Expression("COUNT(p.id)")))
                    ->from(array('r' => 'rovat'))
                    ->join( array ('p' => 'rovat'), 'p.parent = r.id', array('p_id' => 'id', 'p_nev' => 'nev', 'p_slug' => 'slug', 'p_rorder' => 'order', 'p_parent' => 'parent'))
                    ->group('p_parent')
                    ->order('order');
        */
        //print $select->getSqlString();

        $paginatorAdapter = new DbSelect($select, $this->adapter);

        $collection = new RovatokCollection($paginatorAdapter);

        $collection = new RovatokCollection($paginatorAdapter);

        return $collection;
    }

    // public function fetchOne($id)
    // {
    //     $sql = 'SELECT * FROM hirdetes WHERE id = ?';
    //     $resultset = $this->adapter->query($sql, array($id));
    //     $data = $resultset->toArray();
    //     if (!$data) {
    //         return false;
    //     }

    //     $entity = new RovatokEntity();
    //     $entity->exchangeArray($data[0]);
    //     return $entity;
    // }

    // public function create($data)
    // {
    //     //print_r(array_values((array) $data));
    //     //exit;

    //     $sql = 'INSERT INTO hirdetes (szoveg, kep) values(?, ?)';
    //     $resultset = $this->adapter->query($sql, array($data->szoveg, $data->kep));

    //     $data->id = $resultset->getGeneratedValue();

    //     $entity = new RovatokEntity();
    //     $entity->exchangeArray((array)$data);
    //     return $entity;
    // }

    // public function update($data)
    // {
    //     //print_r(array_values((array) $data));
    //     //exit;

    //     $sql = 'UPDATE hirdetes SET szoveg = ?, kep = ? WHERE id = ?';
    //     $this->adapter->query($sql, array($data->szoveg, $data->kep, $data->id));

    //     $entity = new RovatokEntity();
    //     $entity->exchangeArray((array)$data);
    //     return $entity;
    // }

    // public function delete($id)
    // {
    //     $sql = 'DELETE FROM hirdetes WHERE id = ?';
    //     $this->adapter->query($sql, array($id));
    //     return true;
    // }

}