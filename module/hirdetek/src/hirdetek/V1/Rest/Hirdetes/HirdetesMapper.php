<?php
namespace hirdetek\V1\Rest\Hirdetes;

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
                    ->join( array ('i' => 'images'), new Expression('i.ad_id = h.id AND i.sorrend=1'), array('image_id' => 'id', 'image_created' => 'created', 'image_name' => 'name'), 'left')
                    ->join( array ('r' => 'rovat'), 'r.id = h.rovat', array('r_rovat_id' => 'id', 'r_rovat_nev' => 'nev', 'r_rovat_slug' => 'slug'), 'left')
                    ->join( array ('pr' => 'rovat'), 'pr.id = r.parent', array('p_rovat_id' => 'id', 'p_rovat_nev' => 'nev', 'p_rovat_slug' => 'slug'), 'left')
                    ->join( array ('g' => 'regio'), 'g.id = h.regio', array('g_regio_id' => 'id', 'g_regio_nev' => 'nev', 'g_regio_slug' => 'slug'), 'left')
                    ->join( array ('pg' => 'regio'), 'pg.id = g.parent', array('p_regio_id' => 'id', 'p_regio_nev' => 'nev', 'p_regio_slug' => 'slug'), 'left');

        $where = (new Where());

        if ($params->get('userid')) {
            $where->nest->equalTo('h.user_id', $params['userid']);
        }

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
        $sql = "SELECT h.*, i.id as image_id, i.created as image_created, i.name as image_name,
                DATEDIFF(CURDATE(),h.feladas) as days_active,
                r.id as r_rovat_id, r.nev as r_rovat_nev, r.slug as r_rovat_slug,
                pr.id as p_rovat_id, pr.nev as p_rovat_nev, pr.slug as p_rovat_slug,
                g.id as g_regio_id, g.nev as g_regio_nev, g.slug as g_regio_slug,
                pg.id as p_regio_id, pg.nev as p_regio_nev, pg.slug as p_regio_slug,
                IF(pr.id IS NULL, r.id, pr.id) as forovat,
                IF(pr.id IS NULL, 0, r.id) as alrovat,
                IF(pg.id IS NULL, g.id, pg.id) as foregio,
                IF(pg.id IS NULL, -1, g.id) as alregio
                FROM hirdetes h
                LEFT JOIN rovat r ON r.id = h.rovat
                LEFT JOIN rovat pr ON pr.id = r.parent
                LEFT JOIN regio g ON g.id = h.regio
                LEFT JOIN regio pg ON pg.id = g.parent
                LEFT JOIN images i ON i.ad_id = h.id AND i.sorrend = 1
                WHERE h.id = ?";

        $resultset = $this->adapter->query($sql, array($id));

        $data = $resultset->toArray();

        if (!$data) {
            return false;
        }

        $sql = 'SELECT *
                FROM images
                WHERE ad_id = ?
                AND sorrend > 1
                ORDER BY sorrend';

        $resultset = $this->adapter->query($sql, array($id));

        $images = $resultset->toArray();

        $data[0]['images'] = $images;

        $entity = new HirdetesEntity();

        $entity->exchangeArray($data[0]);

        return $entity;
    }

    public function create($data, $user, $id, $filename, $folder_name, $upload_dir, $files)
    {
        //print_r($data);
        //print_r($this->adapter);
        //exit;

        // print "id:" . $id;
        // print "user:";
        // print_r($user);
        // print "filename:" . $filename;

        if($filename && $id && $user) {

            //check if user exists
            $sql = new Sql($this->adapter);

            $select = $sql->select('users')
                          ->columns(array('id'))
                          ->where(array('email' => $user['user_id']))
                          ->order('id')
                          ->limit(1);

            $sqlString = $sql->getSqlStringForSqlObject($select);

            $statement = $sql->prepareStatementForSqlObject($select);

            $resultset = $statement->execute()->current();

            if(is_array($resultset) && isset($resultset['id'])) {

                $user_id = $resultset['id'];

                //check if hirdetes exists and owned by the user
                $sql = new Sql($this->adapter);

                $select = $sql->select('hirdetes')
                              ->columns(array('id'))
                              ->where(array('id' => $id,'user_id' => $user_id))
                              ->order('id')
                              ->limit(1);

                $sqlString = $sql->getSqlStringForSqlObject($select);

                $statement = $sql->prepareStatementForSqlObject($select);

                $resultset = $statement->execute()->current();

                if(is_array($resultset) && isset($resultset['id'])) {

                    $id = $resultset['id'];

                    //save image to file system
                    if(! is_dir($upload_dir . $folder_name)) mkdir($upload_dir . $folder_name, 0755, true);

                    move_uploaded_file($files['tmp_name'], $upload_dir . $folder_name . $filename);

                    //calculate sorrend

                    $sql = new Sql($this->adapter);

                    $select = $sql->select('images')
                                  ->columns(array(new Expression('MAX(sorrend) as max')))
                                  ->where(array('ad_id' => $id));

                    $sqlString = $sql->getSqlStringForSqlObject($select);

                    $statement = $sql->prepareStatementForSqlObject($select);

                    $resultset = $statement->execute()->current();

                    $values['sorrend'] = ++$resultset['max'];

                    //svae image to database

                    $values['ad_id'] = $id;
                    $values['user_id'] = $user_id;
                    $values['name'] = $filename;
                    $values['created'] = new Expression('NOW()');

                    $sql = new Sql($this->adapter);

                    $insert = $sql->insert('images')
                                    ->values($values);

                    $sqlString = $sql->getSqlStringForSqlObject($insert);

                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $resultset = $statement->execute();

                    $image_id = $resultset->getGeneratedValue();

                    return array('success' => true, 'id' => $id, 'image_id' => $image_id, 'message' => 'Kép feltöltve!');
                }
            }

            return array("success" => false, 'id' => $id, 'message' => "A kép feltöltése sikertelen volt!");
        }

        // buzi angular submit buzi object from select hogy baszna szajba a retkes kurva anyjat
        if(isset($data->forovat)) $data->forovat = $data->forovat['id'];
        if(isset($data->alrovat)) $data->alrovat = $data->alrovat['id'];
        if(isset($data->foregio)) $data->foregio = $data->foregio['id'];
        if(isset($data->alregio)) $data->alregio = $data->alregio['id'];

        $inputFilter = new InputFilter();

        $factory = new InputFactory();

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
                        'max'      => 1500,
                    ),
                ),
            ),
        )));

        $inputFilter->add($factory->createInput(array(
            'name'     => 'forovat',
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
            'name'     => 'alrovat',
            'required' => false,
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
            'name'     => 'foregio',
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
            'name'     => 'alregio',
            'required' => false,
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
            'name'     => 'telefon',
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
            'name'     => 'lejarat',
            'required' => true,
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

        $inputFilter->add($factory->createInput(array(
            'name'     => 'szabalyzat',
            'required' => true,
            'filters'  => array(
                array('name' => 'Boolean'),
            ),
        )));


        if(! isset($user)) {

            $email = new Input('email');

            $email->getValidatorChain()
                  ->attach(new Validator\EmailAddress());

            $password = new Input('jelszo');
            $password->getValidatorChain()
                    ->attach(new Validator\StringLength(8));

            $inputFilter->add($email)
                        ->add($password);
        }

        $inputFilter->setData((array)$data);

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

            return array("success" => false, "errors" => $errors);

        }


        //print_r($data);

        if(isset($user)) {

            $sql = new Sql($this->adapter);

            $select = $sql->select('users')
                          ->columns(array('id'))
                          ->where(array('email' => $user['user_id']))
                          ->order('id')
                          ->limit(1);

            $sqlString = $sql->getSqlStringForSqlObject($select);

            $statement = $sql->prepareStatementForSqlObject($select);

            $resultset = $statement->execute()->current();

            $user_id = $resultset['id'];

        } else {

            $user_id = 0;
        }

        $values = array('user_id' => $user_id, 'targy' => $data->targy, 'szoveg' => $data->szoveg);

        if(isset($data->alrovat)) $values['rovat'] = $data->alrovat;
        else $values['rovat'] = $data->forovat;

        if(isset($data->alregio)) $values['regio'] = $data->alregio;
        else $values['regio'] = $data->foregio;

        if(isset($data->telepules)) {
            $values['telepules'] = $data->telepules;
        }

        if(isset($data->ar)) {
            $values['ar'] = $data->ar;
        }

        if(isset($data->telefon)) {
            $values['telefon'] = $data->telefon;
        }

        //feladas es lejarat
        $values['feladas'] = new Expression('NOW()');
        $values['lejarat'] = new Expression('DATE_ADD(NOW(), INTERVAL ' . $data->lejarat . ' DAY)');

        $sql = new Sql($this->adapter);

        $insert = $sql->insert('hirdetes')
                        //->columns(array_keys($values))   // allowed column names for security??? http://framework.zend.com/manual/2.3/en/modules/zend.db.sql.html
                        ->values($values);

        $sqlString = $sql->getSqlStringForSqlObject($insert);

        //print $sqlString;

        $statement = $sql->prepareStatementForSqlObject($insert);
        $resultset = $statement->execute();

        $data->id = $resultset->getGeneratedValue();

        return array("success" => true, "id" => $data->id);

        // $entity = new HirdetesEntity();
        // $entity->exchangeArray((array)$data);
        // return $entity;
    }

    public function update($data)
    {

        $inputFilter = new InputFilter();

        $factory = new InputFactory();

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
                        'max'      => 1500,
                    ),
                ),
            ),
        )));

        $inputFilter->add($factory->createInput(array(
            'name'     => 'forovat',
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
            'name'     => 'alrovat',
            'required' => false,
            'filters'  => array(
                array('name' => 'Digits'),
            ),
            'validators' => array(
                array(
                    'name'    => 'Between',
                    'options' => array(
                        'min' => -1,
                        'max' => 100,
                        'inclusive' => false,
                    ),
                ),
            ),
        )));

        $inputFilter->add($factory->createInput(array(
            'name'     => 'foregio',
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
            'name'     => 'alregio',
            'required' => false,
            'filters'  => array(
                array('name' => 'Digits'),
            ),
            'validators' => array(
                array(
                    'name'    => 'Between',
                    'options' => array(
                        'min' => -1,
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
            'name'     => 'telefon',
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

        $inputFilter->setData((array)$data);

        if ($inputFilter->isValid()) {
            $rovat = (int) $data->alrovat < 1 ? (int) $data->forovat : (int) $data->alrovat;

            $regio = (int) $data->alregio < 1 ? (int) $data->foregio : (int) $data->alregio;

            $sql = 'UPDATE hirdetes
                    SET telepules = ?,
                        targy = ?,
                        szoveg = ?,
                        ar = ?,
                        telefon = ?,
                        rovat = ?,
                        regio = ?
                    WHERE id = ?';

            $this->adapter->query(
                $sql,
                array(
                    $data->telepules,
                    $data->targy,
                    $data->szoveg,
                    $data->ar,
                    $data->telefon,
                    $rovat,
                    $regio,
                    $data->id
                )
            );
        }

        $entity = new HirdetesEntity();
        $entity->exchangeArray((array)$data);

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

            $entity->success = false;
            $entity->errors = $errors;
        }

        //print_r($entity);

        return $entity;
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM hirdetes WHERE id = ?';
        $this->adapter->query($sql, array($id));
        return true;
    }

}