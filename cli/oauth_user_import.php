<?php

/*
 * step1: create users2 table (if does not exists) and import users from users table filtering out duplicate emails
 * step2: import users from users2 table into oauth_users table and creates bcrypt-ed passwords for the oauth
 */

// this script only needed when the application was first deployed
// be very careful before run it again as it can truncate tables!!!
// (although this script can be safely used if there are new users in the users table)
// /however it is not the most efficient to do so/
exit("\nWARNING !!! Only run this script if you know exactly what you are doing !!!! Look into the script to enable the execution !!!\n\n");

// if debug is true then the script in step2 prints out some useful info
//    $debug = true;

$config = include('../config/autoload/user.global.php');

$mysqli = new mysqli('localhost', $config['db']['username'], $config['db']['password'], 'hirdetek');

$mysqli->set_charset("utf8");

/*
 * step1: create users2 table (if does not exists) and import users from users table filtering out duplicate emails
 */

// if create_users2 is true the it truncates the users2 table
// and re-load the users from the users table
// !!! WARNING !!!! THE USERS2 TABLE WILL BE TRUNCATED !!!!
//    $create_users2 = true;

if(isset($create_users2) && $create_users2) {

    $sql = "CREATE TABLE IF NOT EXISTS `users2` (
        `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
        `nev` VARCHAR(50) NOT NULL DEFAULT '',
        `bejnev` VARCHAR(10) NOT NULL DEFAULT '',
        `email` VARCHAR(100) NOT NULL DEFAULT '',
        `telefon` VARCHAR(100) NOT NULL DEFAULT '',
        `varos` VARCHAR(100) NOT NULL DEFAULT '',
        `regio` VARCHAR(100) NOT NULL DEFAULT '',
        `altkategoria` VARCHAR(100) NOT NULL DEFAULT '',
        `weblap` VARCHAR(100) NOT NULL DEFAULT '',
        `felvetel` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `jelszo` VARCHAR(10) NOT NULL DEFAULT '',
        `privilege` TINYINT(4) NOT NULL DEFAULT '0',
        `aktivkod` VARCHAR(100) NOT NULL DEFAULT '',
        `aktiv` INT(11) NOT NULL DEFAULT '0',
        `ipaddr` VARCHAR(20) NOT NULL COLLATE 'utf8_general_ci',
        PRIMARY KEY (`id`),
        INDEX `email` (`email`),
        INDEX `nev` (`nev`),
        INDEX `bejnev` (`bejnev`),
        INDEX `jelszo` (`jelszo`),
        INDEX `aktiv` (`aktiv`)
    )
    COLLATE='utf8_general_ci'
    ENGINE=InnoDB;";

    $result = $mysqli->query($sql);

    $sql = "TRUNCATE users2";

    $result = $mysqli->query($sql);

    $sql = "INSERT INTO users2
            SELECT *
            FROM users u
            GROUP BY email
            ORDER BY id";

    $result = $mysqli->query($sql);

}

/*
 * step2: import users from users2 table into oauth_users table and creates bcrypt-ed passwords for the oauth
 */

// We need ZF auto loading to load the Bcrypt class

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