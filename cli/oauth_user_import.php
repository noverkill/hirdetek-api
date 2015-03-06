<?php

/*
 * import users from users2 table into oauth_users table and creates bcrypt-ed passwords for the oauth
 */

// if set debug is true then the script prints out some useful info
// $debug = false;

$autoload = realpath(__DIR__ . '/../vendor/autoload.php');
if (! $autoload) {
    // Attempt to locate it relative to the application root
    $autoload = realpath(__DIR__ . '/../../../autoload.php');
}

$zf2Env   = "ZF2_PATH";

if (file_exists($autoload)) {
    include $autoload;
} elseif (getenv($zf2Env)) {
    include getenv($zf2Env) . '/Zend/Loader/AutoloaderFactory.php';
    Zend\Loader\AutoloaderFactory::factory(array(
        'Zend\Loader\StandardAutoloader' => array(
            'autoregister_zf' => true
        )
    ));
}

if (!class_exists('Zend\Loader\AutoloaderFactory')) {
    throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
}

$bcrypt = new Zend\Crypt\Password\Bcrypt;


$config = include('../config/autoload/user.global.php');

$mysqli = new mysqli('localhost', $config['db']['username'], $config['db']['password'], 'hirdetek');

$mysqli->set_charset("utf8");


$result = $mysqli->query("SELECT MAX(u.id)
                          FROM oauth_users ou
                          JOIN users2 u ON u.email = ou.username"
                        );
$row = $result->fetch_array();
$result->close();

$max_user_id = (int)$row[0];
$lastid =  $max_user_id;

if(isset($debug) && $debug) print "max_user_id: $max_user_id\n";

if ($result = $mysqli->query("SELECT *
                              FROM users2
                              WHERE email != '' AND id > $max_user_id
                              ORDER BY id
                              LIMIT 1000"
    )) {

    while($row = $result->fetch_array()) {

        $pass = $bcrypt->create($row['jelszo']);

        $sql = "INSERT INTO oauth_users VALUES ('" . $row['email'] . "', '$pass' , '" . $row['nev'] . "', '" . $row['bejnev'] . "')";

        $mysqli->query($sql);

        $lastid =  $row['id'];
    }

    $result->close();
}

if(isset($debug) && $debug) print "lastid: $lastid\n";