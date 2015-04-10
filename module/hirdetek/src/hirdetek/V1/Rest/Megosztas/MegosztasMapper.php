<?php
namespace hirdetek\V1\Rest\Megosztas;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Expression;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Mail;

class MegosztasMapper
{
    protected $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function create($data, $user)
    {
        $user_id = 0;

        $email = $data->email;

        if ($user) {

            $email = $user['user_id'];

            $sql = new Sql($this->adapter);

            $select = $sql->select('users')
                          ->columns(array('id'))
                          ->where(array('email' => $email))
                          ->order('id')
                          ->limit(1);

            $sqlString = $sql->getSqlStringForSqlObject($select);

            $statement = $sql->prepareStatementForSqlObject($select);

            $resultset = $statement->execute()->current();

            if(is_array($resultset) && isset($resultset['id'])) {

                $user_id = $resultset['id'];
            }
        }

        $values = array();

        $values['user_id'] = $user_id;
        $values['sender'] = $email;
        $values['ad_id'] = $data->adid;
        $values['type'] = $data->func;
        $values['message'] = $data->text;
        $values['created'] = new Expression('NOW()');

        $sql = new Sql($this->adapter);

        $insert = $sql->insert('megosztas')
                      ->values($values);

        $sqlString = $sql->getSqlStringForSqlObject($insert);

        //print $sqlString;

        $statement = $sql->prepareStatementForSqlObject($insert);
        $resultset = $statement->execute();

        $data->id = $resultset->getGeneratedValue();

        return array("success" => true, "id" => $data->id);

/*
        $result = new \StdClass();

        $result->success = false;

        $sql = 'SELECT h.*
                FROM hirdetes h
                WHERE h.id = ?';

        $resultset = $this->adapter->query($sql, array($data->id));

        $hird = $resultset->toArray();

        if (!$hird) {
            return $result;
        }
*/

/*
        print_r($hird);
        print "\n";
        print_r($data);
*/

/*
        $mail = new Mail\Message();
        $mail->setFrom($data->sender['email'], $data->sender['name']);
        $mail->addTo($data->recipient['email'], '$data->recipient['name']);
        $mail->setSubject('TestSubject');
        $mail->setBody($hird->szoveg);

        $transport = new Mail\Transport\Sendmail();
        $transport->send($mail);
*/

/*
        $result->success = true;

        return $result;
*/
    }
}