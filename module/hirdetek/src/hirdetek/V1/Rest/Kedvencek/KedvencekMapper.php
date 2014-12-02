<?php
namespace hirdetek\V1\Rest\Kedvencek;

use Zend\Db\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Mail;

class KedvencekMapper
{
    protected $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function create($data, $user_email)
    {

        $sql = 'INSERT INTO kedvenc (user_id, hird_id) SELECT id, ? FROM users WHERE email = ? LIMIT 1';
        $resultset = $this->adapter->query($sql, array($data->id, $user_email));

        $data->new_id = $resultset->getGeneratedValue();

        return $data->new_id;
    }
}