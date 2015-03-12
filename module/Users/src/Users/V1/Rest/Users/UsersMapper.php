<?php
namespace Users\V1\Rest\Users;

use Zend\Db\Sql\Select;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\Input;
use Zend\Validator;

use Zend\Crypt\Password\Bcrypt;

class UsersMapper
{
    protected $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function fetchByEmail($email, $remind)
    {

        $inputFilter = new InputFilter();

        $emailInput = new Input('email');

        $emailInput->getValidatorChain()
              ->attach(new Validator\EmailAddress());

        $inputFilter->add($emailInput);

        $inputFilter->setData(array('email' => $email));

        //if password reminder then return from here
        if($remind) {
            if($inputFilter->isValid()) {
                $sql = 'SELECT jelszo FROM users WHERE email = ? LIMIT 1';
                $resultset = $this->adapter->query($sql, array($email));
                $result = $resultset->toArray();
            }
            //print_r($result);
            if(count($result) > 0) {

                $site = "hirdetek.net";
                $url = "http://hirdetek.net";
                $noreply = "noreply@hirdetek.net";

                // jelszo emlekezteto email kuldese
                $subject = "Jelszó emlékeztető";

                $message = "

Tisztelt regisztrált felhasználónk!

A bejelentkezéshez használja az email címét valamint a következő jelszót: " . $result[0]['jelszo'] . "

Üdvözlettel: a $site csapata

$url

";
                //sendmail($email, $subject, $message, "From: ".$noreply);

            }

            return array();
        }

        $select = new Select('users');

        if ($inputFilter->isValid()) $select->where(array('email' => $email));

        $select->limit(2);

        $paginatorAdapter = new DbSelect($select, $this->adapter);
        $collection = new UsersCollection($paginatorAdapter);
        return $collection;

    }

    public function fetchAll()
    {
        $select = new Select('users');
        $paginatorAdapter = new DbSelect($select->order('id'), $this->adapter);
        $collection = new UsersCollection($paginatorAdapter);
        return $collection;
    }

    public function fetchOne($email)
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

        $inputFilter = new InputFilter();

        $factory = new InputFactory();

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
                        'min'      => 1,
                        'max'      => 70,
                    ),
                ),
            )
        )));

        $email = new Input('email');

        $email->getValidatorChain()
              ->attach(new Validator\EmailAddress());

        $password = new Input('jelszo');
        $password->getValidatorChain()
                ->attach(new Validator\StringLength(6));

        $inputFilter->add($email)
                    ->add($password);

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

        $bcrypt = new Bcrypt;

        $pass = $bcrypt->create($data->jelszo);

        $sql = 'INSERT INTO oauth_users (username, password, first_name, last_name) values(?, ?, ?, ?)';

        $resultset = $this->adapter->query($sql, array($data->email, $pass, $data->nev, ''));

        $aktivkod  = md5(uniqid(rand(), true));

        $sql = 'INSERT INTO users (nev, email, jelszo, felvetel, aktivkod, aktiv) values(?, ?, ?, NOW(), ?, 0)';

        $resultset = $this->adapter->query($sql, array($data->nev, $data->email, $data->jelszo, $aktivkod));

        $data->id = $resultset->getGeneratedValue();

        $site = "hirdetek.net";
        $url = "http://hirdetek.net";
        $noreply = "noreply@hirdetek.net";

        // aktivacios email kuldese
        $message = "

Tisztelt felhasználónk!

Ön regisztrálta magát a ".$url." oldalon.

A regisztráció aktiválását az alábbi link segítségével teheti meg:
".$url."/regisztracio.php?email=" . $data->email . "&kod=$aktivkod

Az aktiválása után a bejelentkezéshez használja az email címét
valamint a következő jelszót: " . $data->jelszo . "

Üdvözlettel: a $site csapata";

/*
Akció!
Ha szeretné, hogy hirdetéseit ezerszer többen lássák, akkor most ezt könnyen elérheti ingyenes
hirdetés kiemeléssel, ráadásul ha kiemeli hirdetését akkor most még egy ingyenes webtárhelyhez is hozzájut.
Részletekért kattinson az alábbi linkre:

$url/kiemeles.php
*/

        //sendmail ($data->email, "Regisztracio", $message, "From: ".$noreply);

        return array("success" => true, "id" => $data->id);

        /*
        $entity = new UsersEntity();
        $entity->exchangeArray((array)$data);
        return $entity;
        */
    }

    public function update($data, $email)
    {

        $inputFilter = new InputFilter();

        $factory = new InputFactory();

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
                        'min'      => 1,
                        'max'      => 70,
                    ),
                ),
            )
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
            'name'     => 'varos',
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

        if(isset($data->password)) {

            $password = new Input('password');
            $password->getValidatorChain()
                     ->attach(new Validator\StringLength(6,20));

            $inputFilter->add($password);
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

        $sql = 'UPDATE users
                SET nev = ?, weblap = ?, telefon = ?, varos = ?, regio = ?, altkategoria = ?
                WHERE id = ? AND email = ?';

        $data->email = $email;

        $this->adapter->query($sql,
            array(
                $data->nev,
                $data->weblap,
                $data->telefon,
                $data->varos,
                $data->regio,
                $data->altkategoria,
                $data->id,
                $data->email
            )
        );

        if(isset($data->password)) {

            $sql = 'UPDATE users SET jelszo = ? WHERE id = ? AND email = ?';

            $this->adapter->query($sql, array($data->password, $data->id, $data->email));

            $bcrypt = new Bcrypt;

            $pass = $bcrypt->create($data->password);

            $sql = 'UPDATE oauth_users SET password = ? WHERE username = ?';

            $this->adapter->query($sql, array($pass, $data->email));
        }

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