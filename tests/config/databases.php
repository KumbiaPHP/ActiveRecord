<?php
//Copiar en app/config/databases.php
//Conexión a Mysql
$databases['mysql'] = $databases['default'] = array(
        'dsn' => 'mysql:host=127.0.0.1;dbname=test;charset=utf8',
        'username' => 'root',
        'password' => '',
        'params' => array(
            //PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', //UTF8 en PHP < 5.3.6
            PDO::ATTR_PERSISTENT => true, //conexión persistente
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        )
);

////Conexión a sqlite ejemplo
//$databases['pgsql'] = array(
//    'dsn' => "sqlite:{APP_PATH}/temp/mydb.sq3",
//    'username' => NULL,
//    'password' => NULL,
//    'params' => array(PDO::ATTR_PERSISTENT => true)
//);

return $databases;
