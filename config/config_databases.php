<?php

//Copiar en app/config/databases.php

return [
    //Conexión a Mysql
    'default' => [
        'dsn'      => 'mysql:host=127.0.0.1;dbname=midatabase;charset=utf8',
        'username' => 'user',
        'password' => 'pass',
        'params'   => [
            //PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', //UTF8 en PHP < 5.3.6
            \PDO::ATTR_PERSISTENT => \true, //conexión persistente
            \PDO::ATTR_ERRMODE    => \PDO::ERRMODE_EXCEPTION
        ]
    ],
    //Conexión a sqlite ejemplo
    'database2' => [
        'dsn'      => "sqlite:{APP_PATH}/temp/mydb.sq3",
        'username' => \null,
        'password' => \null,
        'params'   => [\PDO::ATTR_PERSISTENT => \true]
    ],
    //Conexión a ODBC ejemplo
    'database3' => [
        'dsn'      => 'odbc:testdb',
        'username' => \null,
        'password' => \null,
        'params'   => [
            \PDO::ATTR_CURSOR  => \PDO::CURSOR_FWDONLY,
            \PDO::ATTR_CASE    => \PDO::CASE_LOWER,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ]
    ],
    //Conexión a MSSQL
    'database4' => [
        'dsn'      => 'sqlsrv:Server=mihost;Database=midatabase;',
        'username' => 'miusername',
        'password' => 'mipassword'
        /*'params' => [
        PDO::ATTR_PERSISTENT => true, //conexión persistente
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]*/
    ],
    //Conexión a Oracle
    'oracle' => [
        'dsn'      => 'oci:dbname=//localhost:1521/midatabase',
        'username' => 'username',
        'password' => 'password',
        'params'   => [
            PDO::ATTR_PERSISTENT => true, //conexión persistente
            PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_AUTOCOMMIT => 1,
            PDO::ATTR_CASE       => PDO::CASE_LOWER
        ]
    ]
    //Más conexiones
];
