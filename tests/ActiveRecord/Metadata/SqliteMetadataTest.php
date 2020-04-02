<?php

use Kumbia\ActiveRecord\Db;

/**
 * @requires extension pdo_sqlite
 */
class SqliteMetadataTest extends MetadataTest
{
    
    protected $dbName = 'sqlite';

    /**
     * @beforeClass
     */
    public static function setUpCreateTable()
    {
        Db::get('sqlite')->query('
                CREATE TABLE IF NOT EXISTS test (
                    id int PRIMARY KEY NOT NULL,
                    nombre VARCHAR(50) NOT NULL,
                    email VARCHAR(100) NOT NULL,
                    activo SMALLINT(1) DEFAULT (1)
                );'
        );
    }
}
