<?php

use PHPUnit\Framework\TestCase;
use Kumbia\ActiveRecord\Metadata\Metadata;

class MetadataTest extends TestCase
{
    protected $dbName;

    protected $tableName;

    protected $schemaName;
    
    public function setUp(): void
    {
        $this->dbName     = getenv('DB');

        $this->tableName  = $GLOBALS['metadata_table'];
        $this->schemaName = $GLOBALS['metadata_schema'];
    }

    protected function createClass(): Metadata
    {
        return Metadata::get($this->dbName, $this->tableName, $this->schemaName);
    }

    public function testInstanceOfDriverDb()
    {
        $metadata = $this->createClass();
        $dbDriverClass = \ucfirst($this->dbName).'Metadata';

        $this->assertInstanceOf('\\Kumbia\\ActiveRecord\\Metadata\\'.$dbDriverClass, $metadata);
    }

    public function testGetPK()
    {
        $metadata = $this->createClass();
        $pk = $metadata->getPK();

        $this->assertEquals('id', $pk);
    }

    public function testGetWithDefault()
    {
        $metadata = $this->createClass();
        $withDefault = $metadata->getWithDefault();

        $this->assertEquals(1, count($withDefault));
        $this->assertEquals('activo', $withDefault[0]);
    }

    public function testGetFields()
    {
        $metadata = $this->createClass();
        $fields = $metadata->getFields();

        $this->assertEquals(4, count($fields));

        $fieldList = array_keys($fields);
        $this->assertEquals(['id', 'nombre', 'email', 'activo'], $fieldList);
        
        foreach($fieldList as $fieldName) {
            $this->assertEquals(['Type', 'Null', 'Key', 'Default', 'Auto'], array_keys($fields[$fieldName]));
        }

        $this->fieldData($fields['id'], 'bigint(20)', false, 'PRI', false, true);
        $this->fieldData($fields['nombre'], 'varchar(50)', false, '', false, false);
        $this->fieldData($fields['email'], 'varchar(100)', false, '', false, false);
        $this->fieldData($fields['activo'], 'smallint(1)', true, '', true, false);
    }

    protected function fieldData($field, $type, $null, $key, $default, $auto)
    {
        $this->assertEquals($type, $field['Type']);
        $this->assertEquals($null, $field['Null']);
        $this->assertEquals($key, $field['Key']);
        $this->assertEquals($default, $field['Default']);
        $this->assertEquals($auto, $field['Auto']);
    }

    public function testGetFieldsList()
    {
        $metadata = $this->createClass();
        $fields = $metadata->getFieldsList();

        $this->assertEquals(4, count($fields));

        $this->assertEquals(['id', 'nombre', 'email', 'activo'], $fields);
    }

    public function testGetAutoFields()
    {
        $metadata = $this->createClass();
        $fields = $metadata->getAutoFields();

        
        $this->assertEquals(1, count($fields));

        $this->assertEquals(['id'], $fields);
    }
}
