<?php

use Kumbia\ActiveRecord\Db;

/**
 * @requires extension pdo_pgsql
 */
class PgsqlMetadataTest extends MetadataTest
{
    
    protected $dbName = 'pgsql';

    //TODO return the same than mysql, sqlite to later create auto forms and validations
    protected $expectedGetFields = [
        'id' => [
                'Type' => 'int4', // int
                'Null' => false,
                'Default' => true, //false
                'Key' => 'PRI',
                'Auto' => true,
        ],
        'nombre' => [
                'Type' => 'varchar',
                'Null' => false,
                'Default' => false,
                'Key' => '',
                'Auto' => false,
        ],
        'email' => [
                'Type' => 'varchar',
                'Null' => false,
                'Default' => false,
                'Key' => '',
                'Auto' => false,
        ],
        'activo' => [
                'Type' => 'int2', // smallint(1)
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
        Db::get('pgsql')->query('
                CREATE TABLE IF NOT EXISTS test ( 
                    id serial PRIMARY KEY, 
                    nombre  varchar(50) NOT NULL , 
                    email varchar(100) NOT NULL , 
                    activo smallint NULL DEFAULT 1 
                );'
        );
    }

    //TODO Fix it to delete it
    public function testGetWithDefault()
    {
        $withDefault = $this->getMetadata()->getWithDefault();

        $this->assertEquals(['id', 'activo'], $withDefault);
    }
}
