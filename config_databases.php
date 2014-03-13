<?php
//Copiar en app/config/databases.php
//Conexión a Mysql
$databases['default'] = array(
        'dsn' => 'mysql:host=127.0.0.1;dbname=midatabase;charset=utf8',
        'username' => 'user',
        'password' => 'pass',
        'params' => array(
            //PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', //UTF8 en PHP < 5.3.6
            PDO::ATTR_PERSISTENT => true, //conexión persistente
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        )
);

//Conexión a sqlite ejemplo
$databases['database2'] = array(
    'dsn' => "sqlite:{APP_PATH}/temp/mydb.sq3",
    'username' => NULL,
    'password' => NULL,
    'params' => array(PDO::ATTR_PERSISTENT => true)
);

//Conexión a ODBC ejemplo
$databses['database3'] = array(
        'dsn' => "odbc:testdb",
        'username' => NULL,
        'password' => NULL,
        'params' => array(
            PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY,
            PDO::ATTR_CASE => PDO::CASE_LOWER,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        )
);

//Más conexiones

return $databases; //Siempre al final
