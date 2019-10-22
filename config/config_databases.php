<?php

//Copiar en app/config/databases.php
//Conexión a Mysql
$databases['default'] = [
        'dsn'      => 'mysql:host=127.0.0.1;dbname=midatabase;charset=utf8',
        'username' => 'user',
        'password' => 'pass',
        'params'   => [
            //PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', //UTF8 en PHP < 5.3.6
            PDO::ATTR_PERSISTENT => true, //conexión persistente
            PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
        ],
];

//Conexión a sqlite ejemplo
$databases['database2'] = [
    'dsn'      => 'sqlite:{APP_PATH}/temp/mydb.sq3',
    'username' => null,
    'password' => null,
    'params'   => [PDO::ATTR_PERSISTENT => true],
];

//Conexión a ODBC ejemplo
$databses['database3'] = [
        'dsn'      => 'odbc:testdb',
        'username' => null,
        'password' => null,
        'params'   => [
            PDO::ATTR_CURSOR  => PDO::CURSOR_FWDONLY,
            PDO::ATTR_CASE    => PDO::CASE_LOWER,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ],
];

//Conexión a MSSQL
$databases['database4'] = [
        'dsn'      => 'sqlsrv:Server=mihost;Database=midatabase;',
        'username' => 'miusername',
        'password' => 'mipassword',
        /*'params' => array(
            PDO::ATTR_PERSISTENT => true, //conexión persistente
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        )*/
];

//Conexión a Oracle
$databases['oracle'] = [
    'dsn' => "oci:dbname=//localhost:1521/midatabase",
    'username' => 'username',
    'password' => 'password',
    'params' => [
        PDO::ATTR_PERSISTENT => true, //conexión persistente
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_AUTOCOMMIT => 1,
        PDO::ATTR_CASE => PDO::CASE_LOWER
    ]
];
//Más conexiones

return $databases; //Siempre al final
