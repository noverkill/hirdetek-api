<?php
namespace hirdetek\V1\Rest\Hirdetes;

use Zend\Db\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Predicate\PredicateSet;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\Input;
use Zend\Validator;

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
                    ->join( array ('g' => 'regio'), 'g.id = h.regio', array('g_regio_id' => 'id', 'g_regio_nev' => 'nev', 'g_regio_slug' => 'slug'))
                    ->join( array ('pg' => 'regio'), 'pg.id = g.parent', array('p_regio_id' => 'id', 'p_regio_nev' => 'nev', 'p_regio_slug' => 'slug'));

        $where = (new Where());

        if ($params->get('search')) {
            $where->nest->like('h.szoveg', "%" . $params['search'] . "%");
        }

        if ($params->get('rovat')) {
            $where->nest->equalTo('pr.id', $params['rovat'])->OR->equalTo('r.id', $params['rovat']);
        }

        if ($params->get('regio')) {
            $where->nest->equalTo('pg.id', $params['regio'])->OR->equalTo('g.id', $params['regio']);
        }

        $select = $select->where($where);

        if($minar = $params->get('minar')) {
            $minar = (int) $minar;

            if ($minar >=0 && $minar < 999999999) {
                $select->where("h.ar IS NOT NULL")->where("h.ar >= " . $minar);
            }
        }

        if($maxar = $params->get('maxar')) {
            $maxar = (int) $maxar;

            if ($maxar >=0 && $maxar < 999999999) {
                $select->where("h.ar IS NOT NULL")->where("h.ar <= " . $maxar);
            }
        }

        $ords = array( 'feladas', 'ar');
        $ordirs = array ('DESC', 'ASC');

        //print "ord: " . $params->get('ord');
        //print "ordir: " . $params->get('ordir');

        $ord = in_array($params->get('ord'), $ords) ? $params->get('ord') : 'feladas';
        $ordir = in_array($params->get('ordir'), $ordirs) ? $params->get('ordir') : 'DESC';

        $select->order("$ord $ordir");

        //print $select->getSqlString();
        //exit;

        $paginatorAdapter = new DbSelect($select, $this->adapter);

        $collection = new HirdetesCollection($paginatorAdapter);

        return $collection;
    }

    public function fetchOne($id)
    {
        $sql = 'SELECT h.*,
                DATEDIFF(CURDATE(),h.feladas) as days_active,
                r.id as r_rovat_id, r.nev as r_rovat_nev, r.slug as r_rovat_slug,
                pr.id as p_rovat_id, pr.nev as p_rovat_nev, pr.slug as p_rovat_slug,
                g.id as g_regio_id, g.nev as g_regio_nev, g.slug as g_regio_slug,
                pg.id as p_regio_id, pg.nev as p_regio_nev, pg.slug as p_regio_slug
                FROM hirdetes h
                JOIN rovat r ON r.id = h.rovat
                JOIN rovat pr ON pr.id = r.parent
                JOIN regio g ON g.id = h.regio
                JOIN regio pg ON pg.id = g.parent
                WHERE h.id = ?';

        $resultset = $this->adapter->query($sql, array($id));

        $data = $resultset->toArray();

        if (!$data) {
            return false;
        }

        $entity = new HirdetesEntity();

        $entity->exchangeArray($data[0]);

        return $entity;
    }

    public function create($data, $user)
    {
        //print_r($data);
        //print_r($user);
        //exit;

        /*
        if(! (isset($data->szabalyzat) && $data->szabalyzat == 1)) {
            return array("success" => false, "error" => "Szabályzat!!");
        }

        $fields = array (
            "targy" => array("name" => "Tárgy", "value" => "", "required" => 1), 
            "szoveg" => array("name" => "Szöveg", "value" => "", "required" => 1),
            "rovat" => array("name" => "Kategória", "value" => "", "required" => 1),
            "ar" => array("name" => "Ár", "value" => "", "required" => 0),
            "telepules" => array("name" => "Telelpülés", "value" => "", "required" => 0),
            "regio" => array("name" => "Régió", "value" => "", "required" => 1),
            "telefon" => array("name" => "Telefonszám", "value" => "", "required" => 0)
        );

        foreach($fields as $key => $value) {

            if($value['required'] && ! isset($data->$key)) {
                return array("success" => false, "error" => "Kötelező mezó hiányzik: " . $value['name']);
            } 

            if (isset($data->$key)) {
                $value['value'] = $data->$key;
            }
        }

        //print_r($fields);
        
        return array("success" => true);
        */

        $inputFilter = new InputFilter();

        $factory = new InputFactory();

        //$targy = new Input('targy');

        //$inputFilter->add($targy);

        $inputFilter->add($factory->createInput(array(
            'name'     => 'targy',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
                //array('name' => 'Alnum'), 
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 1,
                        'max'      => 255,
                    ),
                ),
            ),
        )));

        $inputFilter->add($factory->createInput(array(
            'name'     => 'szoveg',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 1,
                        'max'      => 255,
                    ),
                ),
            ),
        )));        

        $inputFilter->add($factory->createInput(array(
            'name'     => 'kategoria',
            'required' => true,
            'filters'  => array(
                array('name' => 'Digits'),
            ),
            'validators' => array(
                array(
                    'name'    => 'Between',
                    'options' => array(
                        'min' => 0,
                        'max' => 100,
                        'inclusive' => false,
                    ),
                ),
            ),
        )));

        $inputFilter->add($factory->createInput(array(
            'name'     => 'regio',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'Between',
                    'options' => array(
                        'min' => 0,
                        'max' => 100,
                        'inclusive' => false,
                    ),
                ),
            ),
        )));        

        $inputFilter->add($factory->createInput(array(
            'name'     => 'telepules',
            'required' => false,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
                //array('name' => 'Alnum'), 
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 0,
                        'max'      => 255,
                    ),
                ),
            ),
        )));

        $inputFilter->add($factory->createInput(array(
            'name'     => 'ar',
            'required' => false,
            'filters'  => array(
                array('name' => 'Digits'),
            ),
            'validators' => array(
                array(
                    'name'    => 'Between',
                    'options' => array(
                        'min' => 1,
                        'max' => 2000000000,  //2 billion
                        'inclusive' => true,
                    ),
                ),
            ),
        )));

        $inputFilter->add($factory->createInput(array(
            'name'     => 'telefons',
            'required' => false,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
                //array('name' => 'Alnum'), 
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 0,
                        'max'      => 255,
                    ),
                ),
            ),
        )));

        $inputFilter->add($factory->createInput(array(
            'name'     => 'ar',
            'required' => false,
            'filters'  => array(
                array('name' => 'Digits'),
            ),
            'validators' => array(
                array(
                    'name'    => 'Between',
                    'options' => array(
                        'min' => 7,
                        'max' => 365,
                        'inclusive' => true,
                    ),
                ),
            ),
        )));

/*
        if(!isset($user)) {

            $email = new Input('email');
            $email->getValidatorChain()
                  ->attach(new Validator\EmailAddress());

            $password = new Input('jelszo');
            $password->getValidatorChain()
                    ->attach(new Validator\StringLength(8));

            $inputFilter->add($email)
                        ->add($password);
        }
*/

        $inputFilter->setData((array)$data);

        $ret = array("success" => true);

        if (! $inputFilter->isValid()) {
            //echo "The form is not valid\n";
            $errors = array();
            foreach ($inputFilter->getInvalidInput() as $error) {
                //print_r($error);//->getMessages());
                $errors[] = array(
                    "field" => $error->getName(),
                    "message" => $error->getMessages()
                );
            }

            $ret = array("success" => false, "errors" => $errors);
        }


        return $ret;

        $sql = 'INSERT INTO hirdetes (szoveg, kep) values(?, ?)';
        $resultset = $this->adapter->query($sql, array($data->szoveg, $data->kep));

        $data->id = $resultset->getGeneratedValue();

        $entity = new HirdetesEntity();
        $entity->exchangeArray((array)$data);
        return $entity;
    }

    public function update($data)
    {
        print 'update';
        print_r(array_values((array) $data));
        exit;

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