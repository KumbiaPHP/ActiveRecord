<?php

use Kumbia\ActiveRecord\Db;

/**
 * @requires extension pdo_sqlite
 */
class SqliteMetadataTest extends MetadataTest
{
    
    protected $dbName = 'sqlite';

    protected $expectedGetFields = [
        'id' => [
                'Type' => 'int',  //int(11)
                'Null' => false,
                'Default' => false,
                'Key' => 'PRI',
                'Auto' => true,
        ],
        'nombre' => [
                'Type' => 'varchar(50)',
                'Null' => false,
                'Default' => false,
                'Key' => '',
                'Auto' => false,
        ],
        'email' => [
                'Type' => 'varchar(100)',
                'Null' => false,
                'Default' => false,
                'Key' => '',
                'Auto' => false,
        ],
        'activo' => [
                'Type' => 'smallint(1)',
                'Null' => true,
                'Default' => true,
                'Key' => '',
                'Auto' => false
        ]
    ];

    /**
     * @beforeClass
     */
    public static function setUpCreateTable()
    {
        Db::get('sqlite')->query('
                CREATE TABLE IF NOT EXISTS test
                 (
                    id int PRIMARY KEY NOT NULL,
                    nombre VARCHAR(50) NOT NULL,
                    email VARCHAR(100) NOT NULL,
                    activo SMALLINT(1) DEFAULT (1)
                );'
        );
    }
}
