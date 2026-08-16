<?php

use Kumbia\ActiveRecord\Db;

require_once __DIR__.'/MetadataTest.php';

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

    public function testGetFields()
    {
        $fields = $this->getMetadata()->getFields();
        $expected = $this->expectedGetFields;

        $fields['id']['Type'] = \preg_replace('/^int(?:\(\d+\))?$/', 'int', $fields['id']['Type']);
        $fields['activo']['Type'] = \preg_replace('/^smallint(?:\(\d+\))?$/', 'smallint', $fields['activo']['Type']);
        $expected['id']['Type'] = 'int';
        $expected['activo']['Type'] = 'smallint';

        $this->assertEquals($expected, $fields);
    }
}
