<?php

use Kumbia\ActiveRecord\Db;

/**
 * @requires extension pdo_mysql
 */
class MysqlMetadataTest extends MetadataTest
{
    
    protected $dbName = 'mysql';

    /**
     * @beforeClass
     */
    public static function setUpCreateTable()
    {
        Db::get('mysql')->query('
                CREATE TABLE IF NOT EXISTS kumbia_test.test ( 
                    id INT(11) NOT NULL AUTO_INCREMENT, 
                    nombre  varchar(50) NOT NULL , 
                    email varchar(100) NOT NULL , 
                    activo smallint(1) NULL DEFAULT 1 , 
                    PRIMARY KEY (id) );'
                );
    }
}
