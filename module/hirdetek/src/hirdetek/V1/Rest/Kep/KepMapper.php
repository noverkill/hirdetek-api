<?php
namespace hirdetek\V1\Rest\Kep;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Expression;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Predicate\PredicateSet;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\Input;
use Zend\Validator;

class KepMapper
{
    protected $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM images WHERE id = ?';
        $this->adapter->query($sql, array($id));
        return true;
    }

}