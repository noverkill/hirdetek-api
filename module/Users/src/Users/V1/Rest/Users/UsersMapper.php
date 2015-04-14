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

    public function checkPostcode($postcode)
    {

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

        $select = new Select('postcodes');

        $inputFilter->setData(array('postcode' => $postcode));

        if($inputFilter->isValid()) {
            $postcode = str_replace(' ', '', $postcode);
            $select->where(array('postcode' => $postcode));
        } else {
            $select->where(array('postcode' => 'fuckoff'));
        }

        $select->limit(2);

        $paginatorAdapter = new DbSelect($select, $this->adapter);
        $collection = new UsersCollection($paginatorAdapter);
        return $collection;
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
                $sql = 'SELECT jelszo,aktiv,aktivkod FROM users WHERE email = ? LIMIT 1';
                $resultset = $this->adapter->query($sql, array($email));
                $result = $resultset->toArray();
            }
            //print_r($result);
            if(count($result) > 0) {

                // jelszo emlekezteto email kuldese

                include('old-config.php');

                $subject = "Jelszó emlékeztető";

                if(! $result[0]['aktiv']) {

                $message = "

Tisztelt regisztrált felhasználónk!

A bejelentkezés elött kérjük aktiválja a felhasználói fiókját az alábbi link segítségével:

".$url."/felhasznalo-aktivalas.php?email=" . $email . "&kod=" . $result[0]['aktivkod'] . "

Az aktiválás után a bejelentkezéshez használja az email címét valamint a következő jelszót: " . $result[0]['jelszo'] . "

Üdvözlettel: a $site csapata

$url

";
                } else {


                $message = "

Tisztelt regisztrált felhasználónk!

A bejelentkezéshez használja az email címét valamint a következő jelszót: " . $result[0]['jelszo'] . "

Üdvözlettel: a $site csapata

$url

";
                }

                //sendmail($email, $subject, $message, "From: ".$noreply);

                $mmessage = new \Zend\Mail\Message();
                $mmessage->setFrom($noreply);
                $mmessage->addTo($email);
                $mmessage->setSubject($subject);

                $body = new \Zend\Mime\Message();

                $textPart = new \Zend\Mime\Part($message);
                $textPart->encoding = \Zend\Mime\Mime::ENCODING_QUOTEDPRINTABLE;
                $textPart->type = "text/plain; charset=UTF-8";

                $body->setParts(array($textPart));

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

            $errors = array();
            foreach ($inputFilter->getInvalidInput() as $error) {
                //print_r($error);//->getMessages());
                $errors[] = array(
                    "field" => $error->getName(),
                    "message" => $error->getMessages()
                );
            }

            if($data->contact && (! isset($errors['field']['nev'])) && (! isset($errors['field']['email']))) {
                // record contact form details

                $ipaddr = $_SERVER['REMOTE_ADDR'];

                $sql = 'INSERT INTO bug (createdon, nev, mail, jelleg, rovleir, leiras, userid, ipaddr) values(NOW(), ?, ?, ?, ?, ?, ?, ?)';

                $resultset = $this->adapter->query($sql, array($data->nev, $data->email, $data->targy, $data->targy, $data->szoveg, $data->userid, $ipaddr));

                $subject = "Kapcsolat felvetel";

                $message = "

userid: " . $data->userid . "
nev:    " . $data->nev . "
email:  " . $data->email . "
targy:  " . $data->targy . "
uzenet: " . $data->szoveg . "
ip cim: $ipaddr

";

                //mail( $admin_mail, $subject, $message, "From: ".$noreply);

                return array("success" => true, "message" => "contact sent", "errors" => $errors, "data" => $data);
            }

            return array("success" => false, "errors" => $errors, "data" => $data);
        }

        $bcrypt = new Bcrypt;

        $pass = $bcrypt->create($data->jelszo);

        $aktivkod  = md5(uniqid(rand(), true));

        $sql = 'INSERT INTO users (nev, email, jelszo, felvetel, aktivkod, weblap, aktiv) values(?, ?, ?, NOW(), ?, ?, 0)';

        $resultset = $this->adapter->query($sql, array($data->nev, $data->email, $data->jelszo, $aktivkod, $pass));

        $data->id = $resultset->getGeneratedValue();

        include('old-config.php');

        // aktivacios email kuldese

        $subject = "$site - regisztráció";

        $message = "

Tisztelt felhasználónk!

Ön regisztrálta magát a ".$url." oldalon.

A regisztráció aktiválását az alábbi link segítségével teheti meg:
".$url."/felhasznalo-aktivalas.php?email=" . $data->email . "&kod=$aktivkod

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

        $mmessage = new \Zend\Mail\Message();
        $mmessage->setFrom($noreply);
        $mmessage->addTo($data->email);
        $mmessage->setSubject($subject);

        $body = new \Zend\Mime\Message();

        $textPart = new \Zend\Mime\Part($message);
        $textPart->encoding = \Zend\Mime\Mime::ENCODING_QUOTEDPRINTABLE;
        $textPart->type = "text/plain; charset=UTF-8";

        $body->setParts(array($textPart));

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