<?php

use PHPUnit\Framework\TestCase;
use Kumbia\ActiveRecord\Metadata\Metadata;

abstract class MetadataTest extends TestCase
{
    
    protected $dbName;

    protected $tableName;

    protected $schemaName;

    protected $expectedGetFields = [
                'id' => [
                        'Type' => 'int(11)',
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
 
            

    public function setUp(): void
    {
        $this->tableName  = getenv('metadata_table');
        $this->schemaName = getenv('metadata_schema');
    }

    protected function getMetadata(): Metadata
    {
        return Metadata::get($this->dbName, $this->tableName, $this->schemaName);
    }

    public function testInstanceOfDriverDb()
    {
        $metadata = $this->getMetadata();
        $dbDriverClass = \ucfirst($this->dbName).'Metadata';

        $this->assertInstanceOf('\\Kumbia\\ActiveRecord\\Metadata\\'.$dbDriverClass, $metadata);
    }

    public function testGetPK()
    {
        $pk = $this->getMetadata()->getPK();

        $this->assertEquals('id', $pk);
    }

    public function testGetWithDefault()
    {
        $withDefault = $this->getMetadata()->getWithDefault();

        $this->assertEquals(['activo'], $withDefault);
    }

    
    public function testGetFields()
    {
        $fields = $this->getMetadata()->getFields();

        $this->assertEquals($this->expectedGetFields, $fields);
    }

    public function testGetFieldsList()
    {
        $fields = $this->getMetadata()->getFieldsList();

        $this->assertEquals(['id', 'nombre', 'email', 'activo'], $fields);
    }

    public function testGetAutoFields()
    {
        $fields = $this->getMetadata()->getAutoFields();
        
        $this->assertEquals(['id'], $fields);
    }
}
//TODO add validation when don't connect to bd and don't get the metadata
// now fail silently
