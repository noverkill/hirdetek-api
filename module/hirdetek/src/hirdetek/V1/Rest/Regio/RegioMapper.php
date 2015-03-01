<?php
namespace hirdetek\V1\Rest\Regio;

use Zend\Db\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;

class RegioMapper
{
    protected $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function fetchAll($params)
    {
        $select = (new Select())->from('regio')->order('order');

        $paginatorAdapter = new DbSelect($select, $this->adapter);

        $collection = new RegioCollection($paginatorAdapter);

        return $collection;
    }

}