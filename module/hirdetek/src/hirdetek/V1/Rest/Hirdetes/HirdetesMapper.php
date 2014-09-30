<?php
namespace hirdetek\V1\Rest\Hirdetes;

use Zend\Db\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;

class HirdetesMapper
{
    protected $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function fetchAll($params)
    {
        $select = (new Select())
                    ->from(array('h' => 'hirdetes'))
                    ->join( array ('r' => 'rovat'), 'r.id = h.rovat', array('r_rovat_id' => 'id', 'r_rovat_nev' => 'nev', 'r_rovat_slug' => 'slug'))
                    ->join( array ('pr' => 'rovat'), 'pr.id = r.parent', array('p_rovat_id' => 'id', 'p_rovat_nev' => 'nev', 'p_rovat_slug' => 'slug'))
                    ->join( array ('g' => 'regio'), 'g.id = h.regio', array('g_regio_id' => 'id', 'g_regio_nev' => 'nev', 'g_regio_slug' => 'slug'));

        if ($params->get('search')) {

            $spec = function (Where $where) use ($params) {

                $where->like('h.szoveg', "%" . $params['search'] . "%");

            };

            $select = $select->where($spec);
        }

        //print $select->getSqlString();
        //exit;

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

    public function update($data)
    {
        //print_r(array_values((array) $data));
        //exit;

        $sql = 'UPDATE hirdetes SET szoveg = ?, kep = ? WHERE id = ?';
        $this->adapter->query($sql, array($data->szoveg, $data->kep, $data->id));

        $entity = new HirdetesEntity();
        $entity->exchangeArray((array)$data);
        return $entity;
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM hirdetes WHERE id = ?';
        $this->adapter->query($sql, array($id));
        return true;
    }

}