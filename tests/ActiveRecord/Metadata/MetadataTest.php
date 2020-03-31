<?php

use PHPUnit\Framework\TestCase;
use Kumbia\ActiveRecord\Metadata\Metadata;

class MetadataTest extends TestCase
{
    /**
     * @return \Kumbia\ActiveRecord\Metadata\Metadata
     */
    protected function createClass()
    {
        $databaseName = $GLOBALS['config_database'];
        $tableName = $GLOBALS['metadata_table'];
        $schemaName = $GLOBALS['metadata_schema'];

        return Metadata::get($databaseName, $tableName, $schemaName);
    }

    public function testInstanceOf()
    {
        $metadata = $this->createClass();

        $this->assertInstanceOf('\\Kumbia\\ActiveRecord\\Metadata\\Metadata', $metadata);
    }

    public function testGetPK()
    {
        $metadata = $this->createClass();
        $pk = $metadata->getPK();

        $this->assertTrue(is_string($pk), 'Debe retornar un string');
        $this->assertEquals('id', $pk);
    }

    public function testGetWithDefault()
    {
        $metadata = $this->createClass();
        $withDefault = $metadata->getWithDefault();

        $this->assertTrue(is_array($withDefault), 'Debe retornar un array');
        $this->assertEquals(1, count($withDefault));
        $this->assertEquals('activo', $withDefault[0]);
    }

    public function testGetFields()
    {
        $metadata = $this->createClass();
        $fields = $metadata->getFields();

        $this->assertTrue(is_array($fields), 'Debe retornar un array');
        $this->assertEquals(4, count($fields));

        $this->assertEquals(['id', 'nombre', 'email', 'activo'], array_keys($fields));
        $this->assertEquals(['Type', 'Null', 'Key', 'Default', 'Auto'], array_keys($fields['id']));
        $this->assertEquals(['Type', 'Null', 'Key', 'Default', 'Auto'], array_keys($fields['nombre']));
        $this->assertEquals(['Type', 'Null', 'Key', 'Default', 'Auto'], array_keys($fields['email']));
        $this->assertEquals(['Type', 'Null', 'Key', 'Default', 'Auto'], array_keys($fields['activo']));

        $this->fieldData($fields['id'], 'int(11)', false, 'PRI', true, false);
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

        $this->assertTrue(is_array($fields), 'Debe retornar un array');
        $this->assertEquals(4, count($fields));

        $this->assertEquals(['id', 'nombre', 'email', 'activo'], $fields);
    }

    public function testGetAutoFields()
    {
        $metadata = $this->createClass();
        $fields = $metadata->getAutoFields();

        $this->assertTrue(is_array($fields), 'Debe retornar un array');
        // @TODO: Revisar, está devolviendo un array vacio
//        $this->assertEquals(4, count($fields));
//
//        $this->assertEquals(array('id', 'nombre', 'email', 'activo'), $fields);
    }
}
