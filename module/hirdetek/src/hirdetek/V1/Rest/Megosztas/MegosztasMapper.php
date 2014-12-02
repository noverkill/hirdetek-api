<?php
namespace hirdetek\V1\Rest\Megosztas;

use Zend\Db\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
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

    public function create($data)
    {

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
        $result->success = true;

        return $result;
    }
}