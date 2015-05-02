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

use Zend\Crypt\Password\Bcrypt;

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
                    ->join( array ('i' => 'images'), new Expression('i.ad_id = h.id AND (i.sorrend=1 OR i.sorrend=111)'), array('image_id' => 'id', 'image_created' => 'created', 'image_name' => 'name', 'image_sorrend' => 'sorrend'), 'left')
                    ->join( array ('r' => 'rovat'), 'r.id = h.rovat', array('r_rovat_id' => 'id', 'r_rovat_nev' => 'nev', 'r_rovat_slug' => 'slug'), 'left')
                    ->join( array ('pr' => 'rovat'), 'pr.id = r.parent', array('p_rovat_id' => 'id', 'p_rovat_nev' => 'nev', 'p_rovat_slug' => 'slug'), 'left')
                    ->join( array ('g' => 'regio'), 'g.id = h.regio', array('g_regio_id' => 'id', 'g_regio_nev' => 'nev', 'g_regio_slug' => 'slug'), 'left')
                    ->join( array ('pg' => 'regio'), 'pg.id = g.parent', array('p_regio_id' => 'id', 'p_regio_nev' => 'nev', 'p_regio_slug' => 'slug'), 'left');

        $geospatial = 0;

        $distance = (int) $params->get('distance');

        $postcode = $params->get('postcode');

        //print "distance: $distance\n";
        //print "postcode: $postcode\n";

        if($postcode && strlen($postcode) > 2 && $distance > 0 && $distance < 51) {

            $inputFilter = new InputFilter();

            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name'     => 'postcode',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                    array('name' => 'StringToUpper'),
                    //array('name' => 'Alnum'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 10,
                        ),
                    ),
                )
            )));

            $inputFilter->setData(array('postcode' => $postcode));

            if ($inputFilter->isValid()) {

                $postcode = strtoupper(str_replace(' ', '', $postcode));

                $sql = new Sql($this->adapter);

                $pselect = $sql->select('postcodes')
                          ->columns(array('postcode', 'latitude', 'longitude'))
                          ->where("postcode LIKE '$postcode%'")
                          ->order('postcode')
                          ->limit(1);

                $sqlString = $sql->getSqlStringForSqlObject($pselect);

                $statement = $sql->prepareStatementForSqlObject($pselect);

                $resultset = $statement->execute()->current();

                //print_r($resultset);

                if(is_array($resultset) && isset($resultset['latitude'])) {

                    $select->join( array (
                        'p' => 'postcodes'),
                        new Expression("p.postcode LIKE CONCAT(h.postcode, '%')"),
                        array('latitude', 'longitude'),
                        'left'
                    );

                    $latitude = $resultset['latitude'];
                    $longitude = $resultset['longitude'];
                    $geospatial = 1;
                }
            }
        }

        //print "geospatial: $geospatial\n";

        $where = (new Where());

		$where->nest->equalTo('h.aktiv', 1);

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

        if($geospatial) {
            //print "latitude: $latitude";
            //print "longitude: $longitude";

            //http://stackoverflow.com/questions/4687312/querying-within-longitude-and-latitude-in-mysql
            //more advanced / efficient: http://stackoverflow.com/questions/1006654/fastest-way-to-find-distance-between-two-lat-long-points
            //What is the distance between a degree of latitude and longitude: http://geography.about.com/library/faq/blqzdistancedegree.htm
            if($distance > 0) {
                //$pfilter = "( 3959 * acos( cos( radians($latitude) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( latitude ) ) ) ) <= $distance";
                $pfilter = "MBRCONTAINS(
                                LineString(
                                    POINT(
                                        $longitude + $distance / ( 53 / COS(RADIANS($latitude))),
                                        $latitude+ $distance / 69
                                    ),
                                    POINT(
                                        $longitude - $distance / (53 / COS(RADIANS($latitude))),
                                        $latitude - $distance / 69
                                    )
                                ),
                                POINT(longitude,latitude)
                            )";

                //print $pfilter;
                $select->where($pfilter);

                $select->where("h.postcode != ''");
            }
        }

        if($postcode && (strlen($postcode) < 3 || $distance == 0)) {

            $select->where("h.postcode like '$postcode%'");
        }

        $ords = array( 'h.lastmodified', 'h.ar');
        $ordirs = array ('DESC', 'ASC');

        //print "ord: " . $params->get('ord');
        //print "ordir: " . $params->get('ordir');

        $ord = in_array($params->get('ord'), $ords) ? $params->get('ord') : 'h.lastmodified';
        $ordir = in_array($params->get('ordir'), $ordirs) ? $params->get('ordir') : 'DESC';

        $select->group("h.id");

        $select->order("h.szponzoralt DESC, $ord $ordir");

        //print $select->getSqlString();
        //exit;

        $paginatorAdapter = new DbSelect($select, $this->adapter);

        $collection = new HirdetesCollection($paginatorAdapter);

        return $collection;
    }

    public function fetchOne($id)
    {
        $sql = "SELECT h.*, i.id as image_id, i.created as image_created, i.name as image_name, i.sorrend as image_sorrend,
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
                LEFT JOIN images i ON i.ad_id = h.id AND (i.sorrend = 1 OR i.sorrend = 111)
                WHERE h.id = ? AND h.aktiv = 1";

        $resultset = $this->adapter->query($sql, array($id));

        $data = $resultset->toArray();

        if (!$data) {
            return false;
        }

        $sql = 'SELECT *
                FROM images
                WHERE ad_id = ?
                AND sorrend > 1 AND sorrend < 111
                ORDER BY sorrend';

        $resultset = $this->adapter->query($sql, array($id));

        $images = $resultset->toArray();

        $data[0]['images'] = $images;

        $entity = new HirdetesEntity();

        $entity->exchangeArray($data[0]);

        return $entity;
    }

    public function create($data, $user, $id, $filename, $folder_name, $upload_dir, $files, $kod)
    {
        //print_r($data);
        //print_r($this->adapter);
        //exit;

        // print "id:" . $id;
        // print "user:";
        // print_r($user);
        // print "filename:" . $filename;

        // kepfeltoltes
        if($filename && $id) {

            $user_id = 0;

            $where = array('id' => '-1');  // just for security set some default
            if ($user) $where = array('email' => $user['user_id']);
            else $where = array('aktivkod' => $kod);

            //check if user exists
            $sql = new Sql($this->adapter);

            $select = $sql->select('users')
                          ->columns(array('id'))
                          ->where($where)
                          ->order('id')
                          ->limit(1);

            $sqlString = $sql->getSqlStringForSqlObject($select);

            $statement = $sql->prepareStatementForSqlObject($select);

            $resultset = $statement->execute()->current();

            if(is_array($resultset) && isset($resultset['id'])) {

                $user_id = $resultset['id'];
            }

            $sql = new Sql($this->adapter);

            $where = array('id' => '-1');   // just for security set some default
            if ($user) $where = array('id' => $id, 'user_id' => $user_id);
            else $where = array('id' => $id, 'aktivkod' => $kod);

            $select = $sql->select('hirdetes')
                          ->columns(array('id'))
                          ->where($where)
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

                //$sqlString = $sql->getSqlStringForSqlObject($insert);

                $statement = $sql->prepareStatementForSqlObject($insert);
                $resultset = $statement->execute();

                $image_id = $resultset->getGeneratedValue();

                return array('success' => true, 'id' => $id, 'image_id' => $image_id, 'message' => 'Picture uploaded successfuly');
            }

            return array("success" => false, 'id' => $id, 'message' => "There was a problem uploading the picture");
        }

        // buzi angular submit buzi object from select hogy baszna szajba a retkes kurva anyjat
        if(isset($data->forovat)) $data->forovat = $data->forovat['id'];
        if(isset($data->alrovat)) $data->alrovat = $data->alrovat['id'];
        //if(isset($data->foregio)) $data->foregio = $data->foregio['id'];
        //if(isset($data->alregio)) $data->alregio = $data->alregio['id'];

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
                        'min'      => 0,
                        'max'      => 70,
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
                        'min'      => 0,
                        'max'      => 10000,
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
            'name'     => 'postcode',
            'required' => false,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
                array('name' => 'StringToUpper'),
                //array('name' => 'Alnum'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 1,
                        'max'      => 10,
                    ),
                ),
            )
        )));

        /*
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
        */

        $inputFilter->add($factory->createInput(array(
            'name'     => 'cim',
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
                        'max'      => 50,
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
                        'max'      => 50,
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
                        'min' => 0,
                        'max' => 3000000000,  //3 billion
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
                        'max'      => 50,
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

        $inputFilter->add($factory->createInput(array(
            'name'     => 'nev',
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
                        'min'      => 0,
                        'max'      => 70,
                    ),
                ),
            ),
        )));

        if(! isset($user)) {

            $email = new Input('email');

            $email->getValidatorChain()
                  ->attach(new Validator\EmailAddress());

            //$password = new Input('jelszo');
            //$password->getValidatorChain()
            //        ->attach(new Validator\StringLength(6));

            $inputFilter->add($email);
            //            ->add($password);
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

            return array("success" => false, "errors" => $errors, "data" => $data);
        }

        $aktivkod = md5(uniqid(rand(), true));

        //print_r($data);

        $values = array(
            'user_id' => 0,
            'nev' => $data->nev,
            'email' => $data->email,
            'targy' => $data->targy,
            'szoveg' => $data->szoveg,
            'aktivkod' => $aktivkod
        );

        $user_message = '';

        if(isset($user)) {

            // bejelentkezett user
            $sql = new Sql($this->adapter);

            $select = $sql->select('users')
                          ->columns(array('id', 'email'))
                          ->where(array('email' => $user['user_id']))
                          ->order('id')
                          ->limit(1);

            //$sqlString = $sql->getSqlStringForSqlObject($select);

            $statement = $sql->prepareStatementForSqlObject($select);

            $resultset = $statement->execute()->current();

            $user_id = $resultset['id'];
            $email   = $resultset['email'];

            $values['user_id'] = $resultset['id'];
            $values['email']   = $resultset['email'];
            $values['aktiv']   = 1;

        } else {

            // check if existing user but not logged in
            // or we need to create a new user
            $sql = new Sql($this->adapter);

            $select = $sql->select('users')
                          ->columns(array('id', 'email', 'jelszo', 'aktiv', 'aktivkod'))
                          ->where(array('email' => $data->email))
                          ->order('id')
                          ->limit(1);

            $statement = $sql->prepareStatementForSqlObject($select);

            $resultset = $statement->execute()->current();

            if(is_array($resultset)) {

                //existing user but not logged in
                $user_id = $resultset['id'];
                $user_aktiv = $resultset['aktiv'];
                $user_jelszo = $resultset['jelszo'];
                $user_aktivkod = $resultset['aktivkod'];
                $email   = $resultset['email'];

                $values['user_id'] = $resultset['id'];
                $values['email']   = $resultset['email'];
                $values['aktiv']   = 0;

                if($user_aktiv) {

                    if(isset($data->atvetel)) {
                        $user_message = '

To edit your Ad please login to your account using your email email address

and the following password: ' . $user_jelszo . '

To list and edit your Ads please click on the "My Ads" link in the top left corner

You can change your password after you logged in on you profile page.
                        ';
                    } else {

                        $user_message = '

As you are a registered user, to edit your Ad, please log in to your account and click on the "My Ads" link in the top left corner.

(In case you forgot your password you can request an email reminder from the login page)
                        ';
                    }

                } else {

                    include_once('old-config.php');

                    if(isset($data->atvetel)) {
                        $user_message = '

To edit your Ad please login to your account using your email email address

and the following password: ' . $user_jelszo . '

To list and edit your Ads please click on the "My Ads" link in the top left corner

You can change your password after you logged in on you profile page.

                        ';
                    } else {

                        $user_message = "

To edit your Ad please first activate your user account using the link below:

".$url."/activate-user.php?email=" . $email . "&code=" . $user_aktivkod . "

then please log in to your account and click on the \"My Ads\" link in the top left corner.

(In case you forgot your password you can request an email reminder from the login page)

                        ";
                    }
                }

            } else {

                // create a new user

                $jelszo = substr($aktivkod, 0, 6);

                $bcrypt = new Bcrypt;
                $pass = $bcrypt->create($jelszo);

                $new_user = array (
                    'nev' => $data->nev,
                    'email' => $data->email,
                    'aktivkod' => $aktivkod,
                    'aktiv' => 0,
                    'jelszo' => $jelszo,
                    'weblap' => $pass,
                    'felvetel' => new Expression('NOW()')
                );

                $insert = $sql->insert('users')
                              ->values($new_user);

                //$sqlString = $sql->getSqlStringForSqlObject($insert);

                $statement = $sql->prepareStatementForSqlObject($insert);
                $resultset = $statement->execute();

                $user_id = $resultset->getGeneratedValue();
                $email   = $data->email;

                $values['user_id'] = $user_id;
                $values['email']   = $email;
                $values['aktiv'] = 0;

                if(isset($data->atvetel)) {

                    $user_message = '

To edit your Ad please login to your account using your email email address

and the following password: ' . $user_jelszo . '

To list and edit your Ads please click on the "My Ads" link in the top left corner

You can change your password after you logged in on you profile page.

                        ';
                } else {

                    $user_message = '

After you have activated your Ad, you can edit it by logging in and then clicking on the \"My Ads\" link in the top left corner.

To log in use your registered email address and the following password: ' . $jelszo . '

You can change your password after you logged in on you profile page.

                    ';
                }
            }
        }

        if(isset($data->alrovat)) $values['rovat'] = $data->alrovat;
        else $values['rovat'] = $data->forovat;

        if(isset($data->cim)) $values['cim'] = $data->cim;

        if(isset($data->postcode)) $values['postcode'] = str_replace(' ', '', $data->postcode);

        //if(isset($data->alregio)) $values['regio'] = $data->alregio;
        //else $values['regio'] = $data->foregio;

        $values['regio'] = 0;

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

        //temporarily
        $values['aktivedon'] = new Expression('NOW()');
        $values['lastmodified'] = new Expression('NOW()');

        $sql = new Sql($this->adapter);

        $insert = $sql->insert('hirdetes')
                        //->columns(array_keys($values))   // allowed column names for security??? http://framework.zend.com/manual/2.3/en/modules/zend.db.sql.html
                        ->values($values);

        $sqlString = $sql->getSqlStringForSqlObject($insert);

        //print $sqlString;

        $statement = $sql->prepareStatementForSqlObject($insert);
        $resultset = $statement->execute();

        $data->id = $resultset->getGeneratedValue();

        if(! isset($user)) {

            include_once('old-config.php');

            if(isset($data->atvetel)) {
                $subject = "Ad submission - ad id #" . $data->id;
            } else {
                $subject = "Ad activation - ad id #" . $data->id;
            }

            $message = "

Welcome!

Thank you for posting an Ad to the $url site

            ";

            if(!isset($data->atvetel)) {
                $message .= "

To activate your Ad please click on the link below:

$url/activate-ad.php?ad=" . $data->id . "&code=$aktivkod
                ";
            }

            $message .= "
$user_message

Best regards,

The $site team

            ";

            //sendmail($email, $subject, $message, "From: ".$noreply);

            // utf-8 multipart email sending w/ or w/out attachment:
            // http://akrabat.com/sending-attachments-in-multipart-emails-with-zendmail/

            if($smtp_send) {
                $mmessage = new \Zend\Mail\Message();
                $mmessage->setFrom($noreply);
                $mmessage->addTo($email);
                $mmessage->setSubject($subject);

                //$mmessage->setEncoding("UTF-8");
                //$mmessage->setBody($message);

                $body = new \Zend\Mime\Message();

                //$htmlPart = new \Zend\Mime\Part($message);
                //$htmlPart->encoding = \Zend\Mime\Mime::ENCODING_QUOTEDPRINTABLE;
                //$htmlPart->type = "text/html; charset=UTF-8";

                $textPart = new \Zend\Mime\Part($message);
                $textPart->encoding = \Zend\Mime\Mime::ENCODING_QUOTEDPRINTABLE;
                $textPart->type = "text/plain; charset=UTF-8";

                $body->setParts(array($textPart));
                //$body->setParts(array($textPart, $htmlPart));

                $mmessage->setBody($body);

                //$messageType = 'multipart/alternative';
                //$mmessage->getHeaders()->get('content-type')->setType($messageType);

                $mmessage->setEncoding('UTF-8');

                $smtpOptions = new \Zend\Mail\Transport\SmtpOptions();
                $smtpOptions->setHost($smtp_host)
                            ->setConnectionClass('login')
                            ->setName($smtp_host)
                            ->setConnectionConfig(array(
                                               'username' => $smtp_user,
                                               'password' => $smtp_password,
                                               'ssl' => $smtp_ssl,
                                               'port' => $smtp_port
                                             )
                                  );

                $transport = new \Zend\Mail\Transport\Smtp($smtpOptions);
                $transport->send($mmessage);

                //constrol email
                $mmessage = new \Zend\Mail\Message();
                $mmessage->setFrom($noreply);
                $mmessage->addTo('sziszi22@yahoo.com');
        		$mmessage->setSubject($subject);
                $body = new \Zend\Mime\Message();
                $textPart = new \Zend\Mime\Part($message . "  " . $email);
                $textPart->encoding = \Zend\Mime\Mime::ENCODING_QUOTEDPRINTABLE;
                $textPart->type = "text/plain; charset=UTF-8";
                $body->setParts(array($textPart));
                //$body->setParts(array($textPart, $htmlPart));
                $mmessage->setBody($body);
                $mmessage->setEncoding('UTF-8');
                $smtpOptions = new \Zend\Mail\Transport\SmtpOptions();
                $smtpOptions->setHost($smtp_host)
                            ->setConnectionClass('login')
                            ->setName($smtp_host)
                            ->setConnectionConfig(array(
                                               'username' => $smtp_user,
                                               'password' => $smtp_password,
                                               'ssl' => $smtp_ssl,
                                               'port' => $smtp_port
                                             )
                                  );
        		$transport = new \Zend\Mail\Transport\Smtp($smtpOptions);
        		$transport->send($mmessage);
            }
        }

        return array("success" => true, "id" => $data->id, "kod" => $aktivkod);

        // $entity = new HirdetesEntity();
        // $entity->exchangeArray((array)$data);
        // return $entity;
    }

    public function update($data, $email)
    {

        //hosszabbitas 30 nappal
        if ($data->lejarat == 30) {

            $sql = 'UPDATE hirdetes
                    SET lejarat = ADDDATE(lejarat, INTERVAL 30 DAY)
                    WHERE id = ?
                    AND email = ?';

            $this->adapter->query(
                $sql,
                array(
                    (int) $data->id,
                    $email
                )
            );

            return $this->fetchOne($data->id);
        }

        $data->forovat = isset( $data->forovat) ? (int) $data->forovat : 0;
        $data->alrovat = isset( $data->alrovat) ? (int) $data->alrovat : 0;
        //$data->foregio = isset( $data->foregio) ? (int) $data->foregio : 0;
        //$data->alregio = isset( $data->alregio) ? (int) $data->alregio : 0;

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
                        'min'      => 0,
                        'max'      => 70,
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
                        'min'      => 0,
                        'max'      => 10000,
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
                        'inclusive' => true,
                    ),
                ),
            ),
        )));

        $inputFilter->add($factory->createInput(array(
            'name'     => 'postcode',
            'required' => false,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
                array('name' => 'StringToUpper'),
                //array('name' => 'Alnum'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 1,
                        'max'      => 10,
                    ),
                ),
            )
        )));
        /*
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
                        'inclusive' => true,
                    ),
                ),
            ),
        )));
        */

        $inputFilter->add($factory->createInput(array(
            'name'     => 'cim',
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
                        'max'      => 50,
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
                        'max'      => 50,
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
                        'min' => 0,
                        'max' => 3000000000,  //3 billion
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
                        'max'      => 50,
                    ),
                ),
            ),
        )));

        //extras
        $inputFilter->add($factory->createInput(array(
            'name'     => 'weblap',
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
                        'min'      => 1,
                        'max'      => 50,
                    ),
                ),
            )
        )));

        $inputFilter->add($factory->createInput(array(
            'name'     => 'szponzoralt',
            'required' => false,
            'filters'  => array(
                array('name' => 'Boolean'),
            ),
        )));

        $inputFilter->add($factory->createInput(array(
            'name'     => 'link_picture',
            'required' => false,
            'filters'  => array(
                array('name' => 'Boolean'),
            ),
        )));

        $inputFilter->add($factory->createInput(array(
            'name'     => 'link_title',
            'required' => false,
            'filters'  => array(
                array('name' => 'Boolean'),
            ),
        )));

        $inputFilter->setData((array)$data);

        if ($inputFilter->isValid()) {

            if($data->alrovat > 0) $data->rovat = $data->alrovat;
            else $data->rovat = $data->forovat;

            if(isset($data->postcode)) $data->postcode = str_replace(' ', '', $data->postcode);
            else $data->postcode = '';

            //if($data->alregio > 0) $data->regio = $data->alregio;
            //else $data->regio = $data->foregio;

            $data->regio = 0;

            $sql = 'UPDATE hirdetes
                    SET postcode = ?,
                        cim = ?,
                        telepules = ?,
                        targy = ?,
                        szoveg = ?,
                        ar = ?,
                        telefon = ?,
                        rovat = ?,
                        regio = ?,
                        lejarat = ?,
                        szponzoralt = ?,
                        weblap = ?,
                        link_picture = ?,
                        link_title = ?
                    WHERE id = ?
                    AND email = ?';

            $this->adapter->query(
                $sql,
                array(
                    $data->postcode,
                    $data->cim,
                    $data->telepules,
                    $data->targy,
                    $data->szoveg,
                    $data->ar,
                    $data->telefon,
                    $data->rovat,
                    $data->regio,
                    $data->lejarat,
                    $data->szponzoralt,
                    $data->weblap,
                    $data->link_picture,
                    $data->link_title,
                    $data->id,
                    $email
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

    public function delete($id, $email)
    {
        $sql = 'DELETE FROM hirdetes WHERE id = ? AND email = ?';
        $this->adapter->query($sql, array($id, $email));
        return true;
    }

}